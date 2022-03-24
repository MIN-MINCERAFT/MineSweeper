<?php

declare(strict_types=1);

namespace Flugins\MineSweeper\Inventory;

use Flugins\MineSweeper\MineSweeper;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;
use skymin\InventoryLib\InvLibAction;
use skymin\InventoryLib\LibInventory;
use skymin\InventoryLib\LibInvType;

final class MineSweeperMainInventory extends LibInventory
{

    public function __construct(private Position $holder)
    {
        parent::__construct(LibInvType::HOPPER(), $this->holder, '지뢰찾기');
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

    protected function onTransaction(InvLibAction $action): void
    {
        $player = $action->getPlayer();
        $action->setCancelled();
        $item = $action->getSourceItem();
        if ($item->getId() === 63) {
            $this->main();
        } else if ($item->getId() === 46) {
            $this->select();
        } else if ($item->getId() === 35) {
            $this->onClose($player);
            MineSweeper::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($item, $player): void {
                if ($item->getMeta() === 5) {
                    $player->setCurrentWindow(new MineSweeperInventory($this->holder, MineSweeper::EASY, LibInvType::CHEST()));
                } else if ($item->getMeta() === 4) {
                    $player->setCurrentWindow(new MineSweeperInventory($this->holder, MineSweeper::NORMAL, LibInvType::DOUBLE_CHEST()));
                } else if ($item->getMeta() === 14) {
                    $player->setCurrentWindow(new MineSweeperInventory($this->holder, MineSweeper::HARD, LibInvType::DOUBLE_CHEST()));
                }
            }), 9);
        }
    }

    public function onClose(Player $who): void
    {
        parent::onClose($who); // TODO: Change the autogenerated stub
    }
}