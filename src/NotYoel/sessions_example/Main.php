<?php

namespace NotYoel\sessions_example;

use pocketmine\plugin\PluginBase;

use NotYoel\sessions_example\database\DatabaseManager;
use NotYoel\sessions_example\sessions\{SessionManager, SessionListener};

use NotYoel\sessions_example\commands\{BalanceCommand, SetBalanceCommand};

class Main extends PluginBase
{

    /* To summarize this session system, when a player joins, we get their data from the database and then create a session with that data.
   If they've joined for the first time, therefore, do not have any data in the database, we add a new row/create the data for them.

   So, when a player has a session, they also have a place in the database.

   During their gameplay, the data (Ex: Money) inside the session can be modified.

   When the player leaves the server, we take the possibly modified data from the session, and we add it back into the database.

   So, you can imagine sessions as a sort of data-holder for a player's information (username, money, etc.) while they are online.
   And, as soon as they leave, we add that data back into the database. Here's a sort of illustration:


   STATE: Database: Holds Player's Data.

   ACTION: Player Joins the Server so, we take the data held by the Database and add it into a session now it looks like this:

   STATE: Session: Holds Player's Data (The data in the session can be modified for example, they could have joined with a balance of 100$ and now only have 50$)

   ACTION: Player Quits the Server so, we take the data held by the Session and add it into the Database. Now it goes back to how it was at the start of the "illustration".


   NOTE: Sessions are used as a way to not directly modify data in the database.

 */

    private static self $instance;

    private DatabaseManager $databaseManager;
    private SessionManager $sessionManager;

    protected function onEnable(): void
    {
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