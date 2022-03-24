<?php

declare(strict_types=1);

namespace Flugins\MineSweeper\Inventory;

use Flugins\MineSweeper\MineSweeper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\Position;
use skymin\InventoryLib\InvLibAction;
use skymin\InventoryLib\LibInventory;
use skymin\InventoryLib\LibInvType;
use function mt_rand;

final class MineSweeperInventory extends LibInventory
{
    public function __construct(Position $holder, private int $land_mine_amount , private LibInvType $type, private bool $isset = false)
    {
        parent::__construct($this->type, $holder, '지뢰찾기');
    }

    public function onOpen(Player $who): void
    {
        parent::onOpen($who);
        if(!$this->isset)
        {
            $this->setUp();
            return;
        }
        for ($i=0; $i<$this->getSize(); $i++) {
            $data = MineSweeper::$player_db[$who->getName()]['item'][$i];
            $item = ItemFactory::getInstance()->get($data['id'], $data['meta'],1,$data['nbt']);
            $this->setItem($i, $item);
        }
    }

    public function onClose(Player $who): void
    {
        parent::onClose($who);
        if($this->type === LibInvType::CHEST())
        {
            $type = true;
        }else{
            $type = false;
        }
        MineSweeper::$player_db[$who->getName()] = [
            'type' => $type,
            'dig' => true, //dig, flag
            'item' => []
        ];
        for ($i=0; $i<$this->getSize(); $i++)
        {
            $item = $this->getItem($i);
            MineSweeper::$player_db[$who->getName()]['item'][] = [
                'id' => $item->getId(),
                'meta' => $item->getMeta(),
                'nbt' => $item->getNamedTag()
            ];
        }
    }

    private function setUp(): void
    {
        $item = ItemFactory::getInstance()->get(ItemIds::PAINTING)->setCustomName('§l§o?');
        $item->setNamedTag(CompoundTag::create()->setString('anything', 'mine'));
        for($i = 0; $i<$this->land_mine_amount; $i++)
        {
            $mt_rand = mt_rand(0,$this->getSize()-10);
            if($this->getItem($mt_rand)->getId() === ItemIds::AIR){
                $this->setItem($mt_rand, $item);
            }else{
                $i--;
            }
        }
        for($i = $this->getSize()-1; $i > $this->getSize()-10; $i--)
        {
            $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::SIGN_POST)->setCustomName(''));
        }
    }

    private function gameover(Player $player): void
    {
        unset(MineSweeper::$player_db[$player->getName()]);
    }

    private function dig(Player $player, Item $item): void
    {
        $nametag= $item->getNamedTag();
        if($nametag->getTag('anything') === null) return;
        if($nametag->getTag('anything') === 'mine')
        {
            $this->gameover($player);
        }
    }

    private function flag(Player $player, Item $item): void
    {

    }

    protected function onTransaction(InvLibAction $action): void
    {
        $player = $action->getPlayer();
        $item = $action->getSourceItem();
        if(MineSweeper::$player_db[$player->getName()]['mod'])
        {
            $this->dig($player, $item);
        }else{

        }
    }
}
