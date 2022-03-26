<?php

declare(strict_types=1);

namespace Flugins\MineSweeper\Inventory;

use Flugins\MineSweeper\MineSweeper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\world\Position;
use skymin\InventoryLib\InvLibAction;
use skymin\InventoryLib\LibInventory;
use skymin\InventoryLib\LibInvType;
use function mt_rand;
use function var_dump;

final class MineSweeperInventory extends LibInventory
{
    private bool $no_work = false;

    public function __construct(Position $holder, private int $land_mine_amount, private LibInvType $type, private bool $isset = false)
    {
        parent::__construct($this->type, $holder, '지뢰찾기');
    }

    public function onOpen(Player $who): void
    {
        parent::onOpen($who);
        $this->sound($who, 'random.levelup');
        if (!$this->isset) {
            $this->setUp($who);
            return;
        }
        for ($i = 0; $i < $this->getSize(); $i++) {
            $data = MineSweeper::$player_db[$who->getName()]['item'][$i];
            $item = ItemFactory::getInstance()->get($data['id'], $data['meta'], 1, $data['nbt']);
            $this->setItem($i, $item);
        }
    }

    public function onClose(Player $who): void
    {
        parent::onClose($who);
        if ($this->no_work) return;
        for ($i = 0; $i < $this->getSize(); $i++) {
            $item = $this->getItem($i);
            MineSweeper::$player_db[$who->getName()]['item'][] = [
                'id' => $item->getId(),
                'meta' => $item->getMeta(),
                'nbt' => $item->getNamedTag()
            ];
        }
    }

    private function setUp(Player $player): void
    {
        $pan = ItemFactory::getInstance()->get(ItemIds::PAINTING, 0, 1);
        if ($this->type === LibInvType::CHEST()) {
            $type = true;
        } else {
            $type = false;
        }
        $pan->setNamedTag(CompoundTag::create()->setString('anything', 'mine')->setInt('flag', 0));
        for ($i = 0; $i < $this->land_mine_amount; $i++) {
            $mt_rand = mt_rand(0, $this->getSize() - 10);
            if ($this->getItem($mt_rand)->getId() === ItemIds::AIR) {
                $this->setItem($mt_rand, $pan);
            } else {
                $i--;
            }
        }
        for ($i = $this->getSize() - 1; $i > $this->getSize() - 10; $i--) {
            $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::SIGN_POST)->setCustomName('§l§f§o벽'));
        }
        if (!isset(MineSweeper::$player_db[$player->getName()])) {
            MineSweeper::$player_db[$player->getName()] = [
                'type' => $type,
                'dig' => true, //dig, flag
                'item' => []
            ];
        }
        if (MineSweeper::$player_db[$player->getName()]['dig']) $itemids = ItemIds::DIAMOND_SHOVEL;
        else $itemids = ItemIds::CHEST_MINECART;
        $this->setItem($this->getSize() - 6, ItemFactory::getInstance()->get($itemids)->setCustomName('§l§6§o깃발 설치 모드로 바꾸기'));
        $this->setItem($this->getSize() - 4, ItemFactory::getInstance()->get(ItemIds::DRIED_KELP)->setCustomName('§l§6§o새 게임 하기'));
        for ($i = 0; $i < $this->getSize() - 10; $i++) {
            $index = 0;
            for ($b=1; $b<5; $b++) {
                if (MineSweeper::getInstance()->getBlockSlot($i,$b,$type) !== null) {
                    $nametag = $this->getItem(MineSweeper::getInstance()->getBlockSlot($i,$b,$type))->getNamedTag();
                    if ($nametag->getTag('anything') !== null) {
                        if ($nametag->getString('anything') === 'mine') $index++;
                    }
                }
            }
            $item = $this->getItem($i)->getNamedTag();
            if ($item->getTag('anything') === null) {
                if ($index === 8) {
                    $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION));
                } else if ($index === 7) {
                    $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::TRIDENT));
                } else if ($index === 6) {
                    $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::TOTEM));
                } else if ($index === 5) {
                    $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::COMMAND_BLOCK_MINECART));
                } else if ($index === 4) {
                    $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::LINGERING_POTION));
                } else if ($index === 3) {
                    $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::BEETROOT_SOUP));
                } else if ($index === 2) {
                    $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::DRAGON_BREATH));
                } else if ($index === 1) {
                    $this->setItem($i, ItemFactory::getInstance()->get(ItemIds::BANNER_PATTERN));
                }
            }
        }
    }

    private function gameover(Player $player): void
    {
        $this->no_work = true;
        $this->onClose($player);
        unset(MineSweeper::$player_db[$player->getName()]);
        $this->sound($player, 'random.explode');
        $player->sendTitle('§l§c게임 오버!', '§f');
    }

    private function sound(Player $player, string $name): void
    {
        $packet = new PlaySoundPacket();
        $packet->soundName = $name;
        $packet->x = $player->getPosition()->getX();
        $packet->y = $player->getPosition()->getY();
        $packet->z = $player->getPosition()->getZ();
        $packet->volume = 1;
        $packet->pitch = 1;
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    private function dig(Player $player, Item $item, int $slot): void
    {
        $nametag = $item->getNamedTag();
        if ($nametag->getTag('anything') === null) return;
        if ($nametag->getInt('flag')) return;
        $this->sound($player, 'dig.grass');
        if ($nametag->getString('anything') === 'mine') {
            $this->gameover($player);
        }
    }

    private function flag(Player $player, CompoundTag $nametag, int $slot): void
    {
        if ($nametag->getTag('flag') === null) return;
        if ($nametag->getInt('flag')) {
            $this->sound($player, 'tile.piston.in');
            $nametag->setInt('flag', 0);
            $this->setItem($slot, ItemFactory::getInstance()->get(ItemIds::PAINTING, 0, 1, $nametag)->setCustomName('§l§o§f[ ? ]'));
        } else {
            $this->sound($player, 'tile.piston.out');
            $nametag->setInt('flag', 1);
            $this->setItem($slot, ItemFactory::getInstance()->get(ItemIds::CHEST_MINECART, 0, 1, $nametag)->setCustomName('§l§o§f[ ? ]'));
        }
    }

    protected function onTransaction(InvLibAction $action): void
    {
        $player = $action->getPlayer();
        $item = $action->getSourceItem();
        $slot = $action->getSlot();
        $action->setCancelled();
        if ($item->getId() === ItemIds::DRIED_KELP) {
            $this->gameover($player);
            return;
        }
        if ($item->getId() === ItemIds::DIAMOND_SHOVEL and $slot === $this->getSize() - 6) {
            MineSweeper::$player_db[$player->getName()]['dig'] = false;
            $this->setItem($this->getSize() - 6, ItemFactory::getInstance()->get(ItemIds::CHEST_MINECART)->setCustomName('§l§6§o지뢰 파기 모드로 바꾸기'));
            return;
        } else if ($item->getId() === ItemIds::CHEST_MINECART and $slot === $this->getSize() - 6) {
            MineSweeper::$player_db[$player->getName()]['dig'] = true;
            $this->setItem($this->getSize() - 6, ItemFactory::getInstance()->get(ItemIds::DIAMOND_SHOVEL)->setCustomName('§l§6§o깃발 설치 모드로 바꾸기'));
            return;
        }
        if (MineSweeper::$player_db[$player->getName()]['dig']) {
            $this->dig($player, $item, $slot);
        } else {
            $this->flag($player, $item->getNamedTag(), $slot);
        }
    }
}
