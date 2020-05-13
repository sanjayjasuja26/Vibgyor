<?php
namespace app\modules\backup\helpers;

use Yii;

class MysqlBackup
{

    public $tables = [];

    public $fp;

    public $file_name;

    public $back_temp_file = 'db_backup_';

    public $_path;

    // this is the break point that we use to break the data in chunks
    const BREACKPOINT = " -- -------AutobackUpStarttoxsl------ ";

    // const BREACKPOINT = " -- -------shahidbhat834@gmail.com------ ";
    public $enableZip = true;

    public function execSqlFile($sqlFile)
    {
        $message = "ok";
        $data = '';
        if (file_exists($sqlFile)) {
            $handle = @fopen($sqlFile, "r");
            if ($handle) {
                $i = 1;
                while (! feof($handle)) {
                    $buffer = fgets($handle);
                    if (strpos($buffer, self::BREACKPOINT) !== false) {
                        $data = trim($data);
                        if ($i == 1) {
                            try {
                                $cmd = \Yii::$app->db->createCommand($data);
                                $cmd->execute();
                            } catch (Exception $e) {
                                $message = $e->getMessage();
                            }
                        } else {
                            // Here the query is being executed in chunks
                            $head = $this->topHeading();
                            $data = PHP_EOL . $head . PHP_EOL . $data . PHP_EOL;
                            $bot = $this->botHeading();
                            $data .= $head . $bot;
                            try {
                                $cmd = \Yii::$app->db->createCommand($data);
                                $cmd->execute();
                            } catch (Exception $e) {
                                $message = $e->getMessage();
                            }
                        }
                        $data = '';
                        $i ++;
                    } else {
                        $data .= $buffer;
                    }
                }
                fclose($handle);
            }
        }
        return $message;
    }

    public function topHeading()
    {
        $str = PHP_EOL . '-- -------------------------------------------' . PHP_EOL . 'SET AUTOCOMMIT=0;' . PHP_EOL . PHP_EOL . 'START TRANSACTION;' . PHP_EOL . 'SET SQL_QUOTE_SHOW_CREATE = 1;' . PHP_EOL . 'SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;' . PHP_EOL . 'SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;' . PHP_EOL . '-- -------------------------------------------' . PHP_EOL;
        return $str;
    }

    public function botHeading()
    {
        $str = PHP_EOL . '-- -------------------------------------------' . PHP_EOL . 'SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;' . PHP_EOL . 'SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;' . PHP_EOL . 'COMMIT;' . PHP_EOL . '-- -------------------------------------------' . PHP_EOL;
        
        return $str;
    }

    public function clean($ignore = ['tbl-user','tbl_user_role',])
    {
        if (! $sql->StartBackup()) {
            return "error";
        }
        
        $message = '';
        
        foreach ($tables as $tableName) {
            if (in_array($tableName, $ignore))
                continue;
            fwrite($this->fp, '-- -------------------------------------------' . PHP_EOL);
            fwrite($this->fp, 'DROP TABLE IF EXISTS ' . addslashes($tableName) . ';' . PHP_EOL);
            fwrite($this->fp, '-- -------------------------------------------' . PHP_EOL);
            
            $message .= $tableName . ',';
        }
        $sql->EndBackup();
        // logout so there is no problme later .
        Yii::$app->user->logout();
        
        $sql->execSqlFile($this->file_name);
        
        unlink($this->file_name);
    }

    public function getTables($dbName = null)
    {
        $sql = 'SHOW TABLES';
        $cmd = Yii::$app->db->createCommand($sql);
        $tables = $cmd->queryColumn();
        return $tables;
    }

    public function startBackup($addcheck = true)
    {
        $this->file_name = $this->getPath() . $this->back_temp_file . date('Y.m.d_H.i.s') . '.sql';
        $this->fp = fopen($this->file_name, 'w+');
        
        if ($this->fp == null)
            return false;
        fwrite($this->fp, '-- -------------------------------------------' . PHP_EOL);
        if ($addcheck) {
            fwrite($this->fp, 'SET AUTOCOMMIT=0;' . PHP_EOL);
            fwrite($this->fp, 'START TRANSACTION;' . PHP_EOL);
            fwrite($this->fp, 'SET SQL_QUOTE_SHOW_CREATE = 1;' . PHP_EOL);
        }
        fwrite($this->fp, 'SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;' . PHP_EOL);
        fwrite($this->fp, 'SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;' . PHP_EOL);
        fwrite($this->fp, '-- -------------------------------------------' . PHP_EOL);
        $this->writeComment('START BACKUP');
        return true;
    }

    public function endBackup($addcheck = true)
    {
        fwrite($this->fp, '-- -------------------------------------------' . PHP_EOL);
        fwrite($this->fp, 'SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;' . PHP_EOL);
        fwrite($this->fp, 'SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;' . PHP_EOL);
        
        if ($addcheck) {
            fwrite($this->fp, 'COMMIT;' . PHP_EOL);
        }
        fwrite($this->fp, self::BREACKPOINT);
        
        fwrite($this->fp, '-- -------------------------------------------' . PHP_EOL);
        $this->writeComment('END BACKUP');
        fclose($this->fp);
        $this->fp = null;
        if ($this->enableZip) {
            
            $this->createZipBackup();
        }
        return $this->file_name;
    }

    public function getColumns($tableName)
    {
        $sql = 'SHOW CREATE TABLE ' . $tableName;
        $cmd = Yii::$app->db->createCommand($sql);
        $table = $cmd->queryOne();
        
        $create_query = $table['Create Table'] . ';';
        
        $create_query = preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $create_query);
        $create_query = preg_replace('/AUTO_INCREMENT\s*=\s*([0-9])+/', '', $create_query);
        if ($this->fp) {
            $this->writeComment('TABLE `' . addslashes($tableName) . '`');
            $final = 'DROP TABLE IF EXISTS `' . addslashes($tableName) . '`;' . PHP_EOL . $create_query . PHP_EOL . PHP_EOL;
            fwrite($this->fp, $final);
        } else {
            $this->tables[$tableName]['create'] = $create_query;
            return $create_query;
        }
    }

    public function getData($tableName)
    {
        $sql = 'SELECT COUNT(*) as rows FROM ' . $tableName;
        $cmd = Yii::$app->db->createCommand($sql);
        $dataReader = $cmd->query();
        $dataReader = $dataReader->read();
        $count = $dataReader['rows'];
        $current = date('Y-m-d H');
        // this is used to break the data in 100 records per section
        $var = 100;
        
        if ($count > $var) {
            // this sql is executed when we have records in a table greater than 100
            $val = '';
            $i = 1;
            $limit = 0;
            while ($count > $var) {
                $sql = 'SELECT * FROM ' . $tableName . ' LIMIT ' . ($limit) . ', 100';
                $cmd = Yii::$app->db->createCommand($sql);
                $dataReader = $cmd->query();
                // the data is being executed by calling this function
                $this->WriteData($dataReader, $tableName);
                $limit = $i * $var;
                $i ++;
                $count = $count - $var;
            }
            if ($count > 0) {
                $sql = 'SELECT * FROM ' . $tableName . ' LIMIT ' . ($limit) . ', 100';
                $cmd = Yii::$app->db->createCommand($sql);
                $dataReader = $cmd->query();
                // the data is being executed by calling this function
                $this->writeData($dataReader, $tableName);
            }
        } else {
            // if the table records are less than 100 records this sql is executed
            if ($count > 0) {
                $sql = 'SELECT * FROM ' . $tableName;
                $cmd = Yii::$app->db->createCommand($sql);
                $dataReader = $cmd->query();
                if ($count > 0) {
                    $val = '';
                    $this->writeData($dataReader, $tableName);
                }
            }
        }
        if ($this->fp)
            fflush($this->fp);
        return true;
    }

    // this function is used to get the backup in chunks on each chunk a breakpoint is inserted
    private function writeData($dataReader, $tableName)
    {
        $val = '';
        $line = PHP_EOL;
        fwrite($this->fp, $line);
        foreach ($dataReader as $data) {
            $itemNames = array_keys($data);
            $itemNames = array_map("addslashes", $itemNames);
            $items = join('`,`', $itemNames);
            
            $itemValues = array_values($data);
            for ($i = 0; $i < count($itemValues); $i ++) {
                if ($itemValues[$i] == '00:00:00' || $itemValues[$i] == '0000-00-00 00:00:00' || $itemValues[$i] == '0000-00-00') {
                    $itemValues[$i] = '';
                }
                $itemValues[$i] = str_replace('\'', '‘', $itemValues[$i]);
                $itemValues[$i] = str_replace('`', '‘', $itemValues[$i]);
            }
            $itemValues = array_map("addslashes", $itemValues);
            
            $valueString = join('","', $itemValues);
            if ($itemValues)
                $val .= "(" . '"' . $valueString . '"' . ")," . PHP_EOL;
        }
        $val = rtrim($val, ",\n");
        if ($val != "") {
            fwrite($this->fp, $line);
            $data_string = "INSERT INTO `$tableName` (`$items`) VALUES" . PHP_EOL . rtrim($val, ",") . ";" . PHP_EOL;
            fwrite($this->fp, $line);
            if ($this->fp) {
                fwrite($this->fp, $data_string);
                fwrite($this->fp, PHP_EOL);
                fwrite($this->fp, self::BREACKPOINT);
            }
        }
    }

    // to implement the multiple table in different files as a backup we can make use of these files
    /*
     *
     * private function getTopHeading($myfile, $tableName)
     * {
     * fwrite($this->fp, $line);
     * fwrite($myfile, '-- -------------------------------------------' . PHP_EOL);
     * fwrite($myfile, 'SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;' . PHP_EOL);
     * fwrite($myfile, 'SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;' . PHP_EOL);
     * fwrite($myfile, '-- -------------------------------------------' . PHP_EOL);
     * fwrite($myfile, '\n');
     * // Write top Comment
     * fwrite($myfile, '-- -------------------------------------------' . PHP_EOL);
     * fwrite($myfile, '-- ' . "Backup Start" . $tableName . PHP_EOL);
     * fwrite($myfile, '-- -------------------------------------------' . PHP_EOL);
     * // end top Comment
     * fwrite($myfile, '\n');
     * }
     * public function getMultiTableData($tableName) {
     * // $tableName = 'tbl_transaction';
     * $sql = 'SELECT COUNT(*) as rows FROM ' . $tableName;
     * $cmd = Yii::$app->db->createCommand($sql);
     * $dataReader = $cmd->query();
     * $dataReader = $dataReader->read();
     * $count = $dataReader['rows'];
     * $current = date('Y-m-d H');
     * $myfile = '';
     * if ($count > 0) {
     * $path = Yii::$app->basePath . '/db/data/' . $current . '/';
     * if (! is_dir($path)) {
     * mkdir($path, 0777, true);
     * }
     * $path = $path . $tableName . '.sql';
     *
     * $myfile = fopen($path, "w+");
     * }
     * if ($count > 3000) {
     * $val = '';
     *
     * $line = "\n";
     * fwrite($this->fp, $line);
     * $this->getTopHeading($myfile, $tableName);
     * fwrite($this->fp, $line);
     *
     * $i = 1;
     * $limit = 0;
     * while ($count > 100) {
     * $sql = 'SELECT * FROM ' . $tableName . ' LIMIT ' . ($limit) . ', 100';
     * $cmd = Yii::$app->db->createCommand($sql);
     * $dataReader = $cmd->query();
     * $this->WriteData($dataReader, $tableName, $myfile);
     * $limit = $i * 100;
     * $i ++;
     * $count = $count - 100;
     * }
     * if ($count == 0) {
     * fwrite($this->fp, $line);
     *
     * $this->getBottomHeading($myfile, $tableName);
     * fwrite($this->fp, $line);
     * }
     * if ($count > 0) {
     * $sql = 'SELECT * FROM ' . $tableName . ' WHERE id > ' . $limit;
     * $cmd = Yii::$app->db->createCommand($sql);
     * $dataReader = $cmd->query();
     * $this->WriteData($dataReader, $tableName, $myfile);
     * }
     * fwrite($this->fp, $line);
     * $this->getBottomHeading($myfile, $tableName);
     * fwrite($this->fp, $line);
     * } else {
     * $sql = 'SELECT COUNT(*) as rows FROM ' . $tableName;
     * $cmd = Yii::$app->db->createCommand($sql);
     * $dataReader = $cmd->query();
     * $count = $dataReader->read();
     * if ($count['rows'] > 0) {
     * $sql = 'SELECT * FROM ' . $tableName;
     * $cmd = Yii::$app->db->createCommand($sql);
     * $dataReader = $cmd->query();
     * if ($count > 0) {
     * $val = '';
     * $line = "\n";
     * fwrite($this->fp, $line);
     * $this->getTopHeading($myfile, $tableName);
     * fwrite($this->fp, $line);
     * $this->WriteData($dataReader, $tableName, $myfile);
     * fwrite($this->fp, $line);
     * $this->getBottomHeading($myfile, $tableName);
     * fwrite($this->fp, $line);
     * }
     * }
     * }
     * return true;
     * }
     *
     *
     * private function getBottomHeading($myfile, $tableName)
     * {
     * fwrite($myfile, '\n');
     * fwrite($myfile, '-- -------------------------------------------' . PHP_EOL);
     * fwrite($myfile, 'SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;' . PHP_EOL);
     * fwrite($myfile, 'SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;' . PHP_EOL);
     * fwrite($myfile, '-- -------------------------------------------' . PHP_EOL);
     * fwrite($myfile, '\n');
     * // Write top Comment
     * fwrite($myfile, '-- -------------------------------------------' . PHP_EOL);
     * fwrite($myfile, '-- ' . "Backup End " . $tableName . PHP_EOL);
     * fwrite($myfile, '-- -------------------------------------------' . PHP_EOL);
     * fwrite($myfile, '\n');
     * // end top Comment
     * fclose($myfile);
     * }
     */
    public function getPath()
    {
        $this->_path = Yii::$app->basePath . '/db/';
        if (! file_exists($this->_path)) {
            @mkdir($this->_path, 0775, true);
        }
        return $this->_path;
    }

    private function writeComment($string)
    {
        fwrite($this->fp, PHP_EOL . '-- -------------------------------------------' . PHP_EOL);
        fwrite($this->fp, PHP_EOL . '-- ' . $string . PHP_EOL);
        fwrite($this->fp, PHP_EOL . '-- -------------------------------------------' . PHP_EOL);
    }

    /**
     * Charge method to backup and create a zip with this
     */
    private function createZipBackup()
    {
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            $file_name = $this->file_name . '.zip';
            if ($zip->open($file_name, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($this->file_name, basename($this->file_name));
                $zip->close();
                
                @unlink($this->file_name);
                $this->file_name = $file_name;
            }
        } else {
            echo "ZipArchive missing class ";
        }
    }

    /**
     * Method responsible for reading a directory and add them to the zip
     *
     * @param ZipArchive $zip
     * @param string $alias
     * @param string $directory
     */
    private function zipDirectory($zip, $alias, $directory)
    {
        if ($handle = opendir($directory)) {
            while (($file = readdir($handle)) !== false) {
                if (is_dir($directory . $file) && $file != "." && $file != ".." && ! in_array($directory . $file . '/', $this->module->excludeDirectoryBackup))
                    $this->zipDirectory($zip, $alias . $file . '/', $directory . $file . '/');
                
                if (is_file($directory . $file) && ! in_array($directory . $file, $this->module->excludeFileBackup))
                    $zip->addFile($directory . $file, $alias . $file);
            }
            closedir($handle);
        }
    }

    /**
     * Zip file execution
     *
     * @param string $zipFile
     *            Name of file zip
     */
    public function unzip($sqlZipFile)
    {
        if (file_exists($sqlZipFile)) {
            $zip = new \ZipArchive();
            $result = $zip->open($sqlZipFile);
            if ($result === true) {
                $zip->extractTo(dirname($sqlZipFile));
                $zip->close();
                $sqlZipFile = str_replace(".zip", "", $sqlZipFile);
            }
        }
        return $sqlZipFile;
    }
}
