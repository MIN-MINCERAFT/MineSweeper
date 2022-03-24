<?php

declare(strict_types=1);

namespace Flugins\MineSweeper;

use Flugins\MineSweeper\Command\MineSweeperCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use skymin\data\Data;
use skymin\InventoryLib\InvLibManager;

final class MineSweeper extends PluginBase
{
    use SingletonTrait;

    private Data $data;

    public static array $db;

    public static array $player_db;

    const EASY = 5;

    const NORMAL = 10;

    const HARD = 15;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->data = new Data($this->getDataFolder() . '/data.json');
        self::$db = $this->data->data;
        $this->getServer()->getCommandMap()->registerAll('MineSweeper', [
            new MineSweeperCommand()
        ]);
        InvLibManager::register($this);
    }

    protected function onDisable(): void
    {
        $this->data->data = self::$db;
        $this->data->save();
    }
}