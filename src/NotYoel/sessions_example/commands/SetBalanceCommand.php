<?php

namespace NotYoel\sessions_example\commands;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use NotYoel\sessions_example\Main;
use NotYoel\sessions_example\sessions\Session;

class SetBalanceCommand extends Command{

    public function __construct(){
        parent::__construct("setbalance", "Set a player's balance", "/setbalance <player> <amount>", ["setbal"]);
        $this->setPermission("setbalance.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!isset($args[0]) or !(($target_player = Server::getInstance()->getPlayerByPrefix($args[0])) instanceof Player)) {
            $sender->sendMessage("Please specify a valid player.");
            return false;
        }

        $session = Main::getInstance()->getSessionManager()->getSession($target_player);

        if(!$session instanceof Session){
            $sender->sendMessage("There's been an error getting {$target_player->getName()}'s session.");
            return false;
        }

        if(!isset($args[1]) or !is_numeric($args[1])){
            $sender->sendMessage("Please specify a valid number.");
            return false;
        }

        $session->setMoney($args[1]);
        $sender->sendMessage("Set {$target_player->getName()}'s balance to $" . number_format($args[1]) . ".");

        return true;
    }
}