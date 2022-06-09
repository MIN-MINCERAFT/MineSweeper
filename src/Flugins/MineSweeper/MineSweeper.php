<?php

declare(strict_types=1);

namespace Flugins\MineSweeper;

use Flugins\MineSweeper\Command\MineSweeperCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use skymin\InventoryLib\InvLibHandler;

final class MineSweeper extends PluginBase
{
    use SingletonTrait;
    use MineSweeperTrait;

    public static array $player_db;

    const EASY = 2;
    const NORMAL = 4;
    const HARD = 10;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->getServer()->getCommandMap()->registerAll('MineSweeper', [
            new MineSweeperCommand()
        ]);
        InvLibHandler::register($this);
    }
}