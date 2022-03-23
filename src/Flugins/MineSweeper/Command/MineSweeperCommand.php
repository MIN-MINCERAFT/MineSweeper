<?php

declare(strict_types=1);

namespace Flugins\MineSweeper\Command;

use Flugins\MineSweeper\Inventory\MineSweeperInventory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class MineSweeperCommand extends Command
{
    public function __construct()
    {
        $this->setPermission('ms.permission');
        parent::__construct('지뢰찾기', '지뢰찾기 관련 명령어입니다', '/지뢰찾기', ['minesweeper','landmine']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        $inv = new MineSweeperInventory($sender->getPosition()->add(0,4,0), 15);
        $inv->send($sender);
    }
}
