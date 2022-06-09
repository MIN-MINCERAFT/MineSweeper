<?php

declare(strict_types=1);

namespace Flugins\MineSweeper\Inventory;

use Flugins\MineSweeper\MineSweeper;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use skymin\InventoryLib\action\InventoryAction;
use skymin\InventoryLib\inventory\BaseInventory;
use skymin\InventoryLib\inventory\InvType;

final class MineSweeperMainInventory extends BaseInventory
{

    public function __construct()
    {
        parent::__construct(InvType::HOPPER(), '지뢰찾기');
    }

    public function onOpen(Player $who): void
    {
        parent::onOpen($who);
        $this->main();
    }

    private function main(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->setItem($i, ItemFactory::getInstance()->get(0));
        }
        $item = ItemFactory::getInstance()->get(46, 0, 1);
        $item->setCustomName('§6§l§o게임 시작하기');
        $this->setItem(2, $item);
    }

    private function select(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->setItem($i, ItemFactory::getInstance()->get(0));
        }
        $index = 1;
        foreach ([5 => '§a초', 4 => '§e중', 14 => '§c고'] as $key => $value) {
            $item = ItemFactory::getInstance()->get(35, $key, 1)->setCustomName("§l§o{$value}급");
            $this->setItem($index, $item);
            $index++;
        }
        $this->setItem(0, ItemFactory::getInstance()->get(63)->setCustomName('§l§o§6메인으로 가기'));
    }

    public function onAction(InventoryAction $action): bool
    {
        $player = $action->getPlayer();
        $item = $action->getSourceItem();
        if ($item->getId() === 63) {
            $this->main();
        } else if ($item->getId() === 46) {
            $this->select();
        } else if ($item->getId() === 35) {
            $this->onClose($player);
            $player->sendTitle('§l§6지뢰를 찾아라!', 'Find the Mines');
            MineSweeper::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($item, $player): void {
                if ($item->getMeta() === 5) {
                    (new MineSweeperInventory(MineSweeper::EASY, InvType::CHEST()))->send($player);
                } else if ($item->getMeta() === 4) {
                    (new MineSweeperInventory( MineSweeper::NORMAL, InvType::DOUBLE_CHEST()))->send($player);
                } else if ($item->getMeta() === 14) {
                    (new MineSweeperInventory( MineSweeper::HARD, InvType::DOUBLE_CHEST()))->send($player);
                }
            }), 14);
        }
        return false;
    }
}
