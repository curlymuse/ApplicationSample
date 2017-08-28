<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;
   /**
    * Define custom actions here
    */

    /**
     * Returns checked attribute of checkbox
     * @param $selector
     * @return bool
     */
   public function seeCheckboxChecked($selector) {
       try {
            $this->seeCheckboxIsChecked($selector);
       }catch (\PHPUnit_Framework_AssertionFailedError $f){
           return false;
       }

       return true;
   }
}
