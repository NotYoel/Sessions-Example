<?php

declare(strict_types=1);

namespace NotYoel\sessions_example\database;

use Closure;
use NotYoel\sessions_example\libs\_9d59f7f38d353f3a\poggit\libasynql\libasynql;
use NotYoel\sessions_example\libs\_9d59f7f38d353f3a\poggit\libasynql\SqlError;

class DatabaseManager
{
    private $plugin;
    private $database;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;

    }

    public function loadDatabase()
    {
        $this->database = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql"
        ]);
        $this->database->executeGeneric("table.users");
        $this->database->waitAll();

    }

    public function closeDatabase()
    {
        if (isset($this->database)) $this->database->close();
    }

    public function addPlayer($xuid, $name, $money)
    {
        $this->database->executeInsert("add.player", [
            "xuid" => $xuid,
            "username" => $name,
            "money" => $money
        ], null,
            fn(SqlError $err) => $this->plugin->getLogger()->error($err->getMessage()));
    }

    public function getPlayer(string $xuid, Closure $callback)
    {
        $this->database->executeSelect("get.player", [
            "xuid" => $xuid
        ], function (array $rows) use ($callback) {
            ($callback)($rows);
        });
    }


}