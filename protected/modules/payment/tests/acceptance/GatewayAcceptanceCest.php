<?php
namespace payment;

use AcceptanceTester;
use Helper;

class GatewayAcceptanceCest

{

    public $id;

    protected $data = [];

    public function _data()
    {
        $this->data['title'] = \Helper::faker()->text(10);
        /* $this->data['value'] = \Helper::faker()->text; */
        $this->data['mode'] = 0;
        $this->data['state_id'] = 1;
        $this->data['type_id'] = 0;
    }

    public function _before(AcceptanceTester $I)
    {
        Helper::login($I);
    }

    public function _after(AcceptanceTester $I)
    {}

    public function IndexWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/payment/gateway/index');
        $I->canSeeResponseCodeIs(200);
        $I->seeElement('.grid-view');
        $this->_data();
    }

    public function AddFormCanBeSubmittedEmpty(AcceptanceTester $I)
    {
        $I->amOnPage('/payment/gateway/add');
        $I->seeElement('#payment-gateway-form');
        $I->amGoingTo('add form without credentials');
        $I->click('#payment-gateway-form-submit');
        $I->canSeeResponseCodeIs(200);
        $I->expectTo('see validations errors');
        $req = $I->grabMultiple('.required');
        $count = count($req) - 2;
        $I->seeNumberOfElements('.has-error', $count);
    }

    public function AddWorksWithData(AcceptanceTester $I)
    {
        $I->amOnPage('/payment/gateway/add');
        $I->seeElement('#payment-gateway-form');
        $I->amGoingTo('add form with right data');
        
        $I->fillField('Gateway[title]', $this->data['title']);
        /* $I->fillField ('Gateway[value]',$this->data['value'] ); */
        $I->selectOption('Gateway[mode]', $this->data['mode']);
        $I->selectOption('Gateway[state_id]', $this->data['state_id']);
        $I->selectOption('Gateway[type_id]', $this->data['type_id']);
        $I->click('#payment-gateway-form-submit');
        $I->canSeeResponseCodeIs(200);
        $this->id = $I->grabFromCurrentUrl('/[=\/](\d+)/');
    }

    public function DetailsWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/payment/gateway/detail?id=' . $this->id);
        
        $I->fillField('Value[username]', 'user');
        $I->fillField('Value[password]', 'value123');
        $I->fillField('Value[signature]', 'user');
        $I->fillField('Value[client_id]', '123');
        $I->fillField('Value[secret_key]', '123456');
        $I->click('#payment-gateway-form-submit');
        $I->canSeeResponseCodeIs(200);
        $I->dontseeElement('#payment-gateway-form');
        $I->amGoingTo('View gateway details');
        $I->canSeeResponseCodeIs(200);
        $I->see(array_key_exists('title', $this->data) ? $this->data['title'] : '', 'h1');
        $I->seeElement('.table-bordered');
    }

    public function ViewWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/payment/gateway/detail?id=' . $this->id);
        $I->amGoingTo('View gateway details');
        $I->canSeeResponseCodeIs(200);
    }

    public function UpdateWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/payment/gateway/update?id=' . $this->id);
        $I->seeElement('#payment-gateway-form');
        $I->amGoingTo('add form with right data');
        $I->fillField('Gateway[title]', $this->data['title']);
        $I->selectOption('Gateway[mode]', $this->data['mode']);
        $I->fillField('Value[username]', 'user');
        $I->fillField('Value[password]', 'value123');
        $I->fillField('Value[signature]', 'user');
        $I->fillField('Value[client_id]', '123');
        $I->fillField('Value[secret_key]', '123456');
        $I->click('#payment-gateway-form-submit');
        $I->canSeeResponseCodeIs(200);
        $I->dontseeElement('#gateway-form');
        $I->see(array_key_exists('title', $this->data) ? $this->data['title'] : '', 'h1');
        $I->seeElement('.table-bordered');
    }

    public function DeleteWorks(AcceptanceTester $I)
    {
        $I->sendAjaxPosSRequest('/payment/gateway/delete/' . $this->id);
        $I->expectTo('delete gateway works');
        $I->amOnPage('/paymentgateway/' . $this->id);
        $I->canSeeResponseCodeIs(404);
    }
}
