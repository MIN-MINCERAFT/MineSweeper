<?php

declare(strict_types=1);

namespace Flugins\MineSweeper;

use Flugins\MineSweeper\Command\MineSweeperCommand;
use pocketmine\plugin\PluginBase;
use skymin\data\Data;
use skymin\InventoryLib\InvLibManager;

final class MineSweeper extends PluginBase
{
    private Data $data;

    public static array $db;

    protected function onEnable(): void
    {
        $this->data = new Data($this->getDataFolder() . '/data.json');
        self::$db = $this->data->data;
        InvLibManager::register($this);
        $this->getServer()->getCommandMap()->registerAll('MineSweeper', [
            new MineSweeperCommand()
        ]);
    }

    protected function onDisable(): void
    {
        $this->data->data = self::$db;
        $this->data->save();
    }
}