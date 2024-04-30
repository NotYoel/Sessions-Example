<?php

namespace NotYoel\sessions_example\commands;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use NotYoel\sessions_example\Main;
use NotYoel\sessions_example\sessions\Session;

class BalanceCommand extends Command{

    public function __construct(){
        parent::__construct("balance", "Check a player's balance", "/balance <player>", ["bal"]);
        $this->setPermission("balance.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(isset($args[0])){
            $target_name = $args[0];

            if(!(($target_player = Server::getInstance()->getPlayerByPrefix($target_name)) instanceof Player)){
                $sender->sendMessage("{$target_name} is not online. Please try again later.");
                return false;
            }

            $session = Main::getInstance()->getSessionManager()->getSession($target_player);

            if(!$session instanceof Session){
                $sender->sendMessage("There's been an error getting {$target_player->getName()}'s session.");
                return false;
            }

            $sender->sendMessage("{$target_player->getName()} currently has a balance of: $" . number_format($session->getMoney()) . ".");

        } else {
            if(!$sender instanceof Player){
                $sender->sendMessage("You can only use this command in-game.");
                return false;
            }

            $session = Main::getInstance()->getSessionManager()->getSession($sender);

            if(!$session instanceof Session){
                $sender->sendMessage("There's been an error getting your session.");
                return false;
            }

            $sender->sendMessage("You currently have a balance of: $" . number_format($session->getMoney()) . ".");
        }

        return true;
    }
}