<?php

namespace NotYoel\sessions_example;

use pocketmine\plugin\PluginBase;

use NotYoel\sessions_example\database\DatabaseManager;
use NotYoel\sessions_example\sessions\{SessionManager, SessionListener};

use NotYoel\sessions_example\commands\{BalanceCommand, SetBalanceCommand};

class Main extends PluginBase{

    private static self $instance;

    private DatabaseManager $databaseManager;
    private SessionManager $sessionManager;

    protected function onEnable() : void{
        // Here we just initialize/register everything (SessionManager, DatabaseManager)

        /* Classes like DatabaseManager and SessionManager are assigned to properties
           like $this->databaseManager and $this->sessionManager, so then we can get an
           instance of both classes when calling their respective functions.
        */
        self::$instance = $this;

        $this->databaseManager = new DatabaseManager($this);
        $this->databaseManager->loadDatabase();

        $this->sessionManager = new SessionManager();
        $this->getServer()->getPluginManager()->registerEvents(new SessionListener(), $this);

        $this->getServer()->getCommandMap()->register("balance", new BalanceCommand());
        $this->getServer()->getCommandMap()->register("setbalance", new SetBalanceCommand());

        $this->getLogger()->info("Sessions have been enabled.");
    }

    protected function onDisable() : void{
        $this->getLogger()->info("Sessions have been disabled.");
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getDatabaseManager() : DatabaseManager{
        return $this->databaseManager;
    }

    public function getSessionManager() : SessionManager{
        return $this->sessionManager;
    }
}