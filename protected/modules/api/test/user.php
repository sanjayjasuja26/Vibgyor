     <?php
					return [ 
							'user' => [ 
									
									'login' => [ 
											'LoginForm[username]' => 'harman',
											'LoginForm[password]' => 'admin',
											'LoginForm[device_token]' => '12131313',
											'LoginForm[device_type]' => '1'
									] ,
									'signup' => [
											'User[full_name]' => 'Test String',
											'User[email]' => 'Trand'.rand(0,499).'est@'.rand(0,499).'String.com',
											'User[password]' => 'Test String',
											'User[contact_no]' => 'Test String',
									        'User[affiliate_code]' => 'testString',
											
									],
							    'update' => [
							        'User[full_name]' => 'Test String',
							        'User[email]' => 'Trand'.rand(0,499).'est@'.rand(0,499).'String.com',
							        'User[profile_file]' => 'Test String',
							    ],
									'change-password' => [
											'User[oldPassword]' => 'Test String',
											'User[newPassword]' => 'Test String',
											'User[confirm_password]' => 'Test String',
												
									],
									'instagram' => [
											"User[email]"=>"",
											"User[userId]"=>"",
											"User[provider]"=>"",
											"User[full_name]"=>"",
											//"User[image_url]"=>'',
											"User[device_token]"=>'',
											"User[device_type]"=>''
									],
							]
							 
					];
					?>
