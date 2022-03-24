<?php

declare(strict_types=1);

namespace Flugins\MineSweeper\Command;

use Flugins\MineSweeper\Inventory\MineSweeperInventory;
use Flugins\MineSweeper\Inventory\MineSweeperMainInventory;
use Flugins\MineSweeper\MineSweeper;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;
use skymin\InventoryLib\LibInvType;

final class MineSweeperCommand extends Command
{
    public function __construct()
    {
        $this->setPermission('ms.permission');
        parent::__construct('지뢰찾기', '지뢰찾기 관련 명령어입니다', '/지뢰찾기', ['minesweeper', 'landmine']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return;
        if (!$this->testPermission($sender)) return;
        $position = $sender->getPosition();
        $pos = new Position($position->getX(), $position->getY() + 3, $position->getZ(), $position->getWorld());
        $name = $sender->getName();
        if (isset(MineSweeper::$player_db[$name])) {
            if(MineSweeper::$player_db[$name]['type'])
            {
                $inv = new MineSweeperInventory($pos, MineSweeper::NORMAL, LibInvType::CHEST(), true);
            }else{
                $inv = new MineSweeperInventory($pos, MineSweeper::NORMAL, LibInvType::DOUBLE_CHEST(), true);
            }
            $inv->send($sender);
        } else {
            $inv = new MineSweeperMainInventory($pos);
            $inv->send($sender);
        }
    }
}
