<?php

declare(strict_types=1);

namespace Flugins\MineSweeper\Inventory;

use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\world\Position;
use skymin\InventoryLib\InvLibAction;
use skymin\InventoryLib\LibInventory;
use skymin\InventoryLib\LibInvType;
use function mt_rand;

final class MineSweeperInventory extends LibInventory
{
    private int $land_mine_amount;

    public function __construct(Position $holder, int $land_mine_amount)
    {
        $this->land_mine_amount = $land_mine_amount;
        parent::__construct(LibInvType::CHEST(), $holder, '지뢰찾기');
    }

    public function onOpen(Player $who): void
    {
        $item = ItemFactory::getInstance()->get(46, 0, 1);
        for($i = 0; $i<$this->land_mine_amount; $i++)
        {
            $mt_rand = mt_rand(0,48);
            if($this->getItem($mt_rand)->getId() === 0){
                $i--;
            }else{
                $this->setItem($mt_rand, $item);
            }
        }
        parent::onOpen($who);
    }

    protected function onTransaction(InvLibAction $action): void
    {
    }
}
