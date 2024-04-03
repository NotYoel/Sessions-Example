<?php

namespace NotYoel\sessions_example\sessions;

use pocketmine\player\Player;

use NotYoel\sessions_example\Main;

class SessionManager{

    /* This is where we store all the sessions, so then we can get them later.
       This array would look somewhat like this:
       [
            "Player1's XUID" => Player1's Session Class,
            "Player2's XUID" => Player2's Session Class,
            "Player3's XUID" => Player3's Session Class
       ]
    */
    private array $sessions = [];

    public function createSession(Player $player) : void{
        $xuid = $player->getXuid();
        $username = $player->getName();

        $db = Main::getInstance()->getDatabaseManager()->getDatabase();

        /* Here we get the player's data from the database.
           $result will return either an array containing the
           player's data or 'False' if the player isn't in
           the database yet, therefore, they've just logged into
           the server for the first time.
        */
        $stmt = $db->prepare("SELECT * FROM player WHERE xuid=:xuid");
        $stmt->bindParam(':xuid', $xuid);

        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        
        $stmt->close();

        if(is_array($result)){
            /* $result is an array, so it looks like this:
               [
                    "xuid" => Player's XUID,
                    "username" => Player's Username,
                    "money" => Player's Money
               ]
            */

            /* We get the player's money by indexing and assign it to a variable so then.
               we can use it when creating the session class down below.
            */
            $money = $result["money"];
        } else {
            // The player joined for the first time so $money is set to 0.
            $money = 0;

            /* Since they don't have any data in the database, we add it by
               inserting a new row into the database.
             */
            $stmt = $db->prepare("INSERT OR REPLACE INTO player(xuid, username, money) VALUES(:xuid, :username, :money)");
            $stmt->bindParam(':xuid', $xuid);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':money', $money);

            $stmt->execute();
            $stmt->close();
        }

        // We create a new 'Session' class and add it into the global 'sessions' property
        $session = new Session($xuid, $username, $money);
        $this->sessions[$xuid] = $session;
    }

    public function closeSession(Player $player) : void{
        /* The if statement is used so then if the player joins and quits on the
           "Locating server..." menu, this stops it from throwing an
           error. (Because normally, PlayerQuitEvent is called, therefore running closeSession() without
           a session being created, therefore attempting to close a non-existent session.)
        */
        if(($session = $this->getSession($player)) instanceof Session){
            // We get the player's data back.
            $xuid = $player->getXuid();
            $username = $player->getName();
            $money = $session->getMoney();

            // We add it back into the database before closing it.
            $stmt = Main::getInstance()->getDatabaseManager()->getDatabase()->prepare("INSERT OR REPLACE INTO player(xuid, username, money) VALUES(:xuid, :username, :money)");
            $stmt->bindParam(':xuid', $xuid);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':money', $money);

            $stmt->execute();
            $stmt->close();

            // We remove the player from the global 'sessions' property because they've left the server.
            unset($this->sessions[$player->getXuid()]);
        }
    }

    /* This can be used when trying to get a player's session, so then you can get stats like
       their money.
    */
    public function getSession(Player $player) : ?Session{
        return $this->sessions[$player->getXuid()] ?? null;
    }
}