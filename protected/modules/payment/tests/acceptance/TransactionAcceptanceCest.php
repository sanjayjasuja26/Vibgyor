<?php
namespace payment;

use AcceptanceTester;
use Helper;

class TransactionAcceptanceCest

{

    public $id;

    protected $data = [];

    public function _data()
    {
        /* $this->data['name'] = \Helper::faker()->text(10); */
        /* $this->data['email'] = \Helper::faker()->email; */
        /* $this->data['model_id'] = 1; */
        /* $this->data['model_type'] = \Helper::faker()->text(10); */
        /* $this->data['amount'] = \Helper::faker()->text(10); */
        $this->data['currency'] = \Helper::faker()->text(10);
        /* $this->data['transaction_id'] = 1; */
        /* $this->data['payer_id'] = 1; */
        /* $this->data['value'] = \Helper::faker()->text; */
        /* $this->data['gateway_type'] = \Helper::faker()->text(10); */
        /* $this->data['payment_status'] = \Helper::faker()->text(10); */
    }

    public function _before(AcceptanceTester $I)
    {
        Helper::login($I);
    }

    public function _after(AcceptanceTester $I)
    {}

    public function IndexWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/payment/transaction/index');
        $I->canSeeResponseCodeIs(200);
        $I->seeElement('.grid-view');
        $this->_data();
    }
}
