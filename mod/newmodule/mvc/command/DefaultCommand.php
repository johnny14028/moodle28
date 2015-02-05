<?php
require_once(__DIR__.'/../command/Command.php');
require_once(__DIR__.'/../controller/Request.php');
class mvc_command_DefaultCommand extends mvc_command_Command
{
    public function doExecute(mvc_controller_Request $objRequest)
    {
        $objRequest->addFeedback("Bienvenido: Moodle usando MVC");
    }
}