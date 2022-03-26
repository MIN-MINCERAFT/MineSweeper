<?php

declare(strict_types=1);

namespace Flugins\MineSweeper;

use Flugins\MineSweeper\Command\MineSweeperCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use skymin\InventoryLib\InvLibManager;

final class MineSweeper extends PluginBase
{
    use SingletonTrait;

    public static array $player_db;

    const EASY = 4;

    const NORMAL = 8;

    const HARD = 13;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->getServer()->getCommandMap()->registerAll('MineSweeper', [
            new MineSweeperCommand()
        ]);
        InvLibManager::register($this);
    }

    public function getBlockSlot(int $slot, int $pos, bool $type= false): ?int
    {
        //1:up 2:left 3:right 4:down
        $match = null;
        if($pos === 1)
        {
            if($type){
                $match = match ($slot) {
                    9 => 0, 10 => 1, 11 => 2, 12 => 3, 13 => 4, 14 => 5, 15 => 6, 16 => 7, 17 => 8,
                    default => null
                };
            }else{
                $match = match ($slot) {
                    9 => 0, 10 => 1, 11 => 2, 12 => 3, 13 => 4, 14 => 5, 15 => 6, 16 => 7, 17 => 8,
                    18 => 9, 19=> 10, 20=>11, 21 => 12, 22=>13, 23 => 14, 24=>15, 25=>16, 26=>17,
                    27 => 18, 28=> 19, 29=>20, 30 => 21, 31=>22, 32 => 23, 33=>24, 34=>25, 35=>26,
                    36 => 27, 37=> 28, 38=>29, 39 => 30, 40=>31, 41 => 32, 42=>33, 43=>34, 44=>35,
                    default => null
                };
            }
        }else if ($pos === 2)
        {
            if($type){
                $match = match ($slot) {
                    1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,
                    10=>9,11=>10,12=>11,13=>12,14=>13,15=>14,16=>15,17=>16,
                    default => null
                };
            }else{
                $match = match ($slot) {
                    1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,
                    10=>9,11=>10,12=>11,13=>12,14=>13,15=>14,16=>15,17=>16,
                    19=>18,20=>19,21=>20,22=>21,23=>22,24=>23,25=>24,26=>25,
                    28=>27,29=>28,30=>29,31=>30,32=>31,33=>32,34=>33,35=>34,
                    37=>36,38=>37,39=>38,40=>39,41=>40,42=>41,43=>42,44=>43,
                    default => null
                };
            }
        }else if ($pos === 3)
        {
            if($type){
                $match = match ($slot) {
                    0=> 1, 1=>2,2=>3,3=>4,4=>5,5=>6,6=>7,7=>8,
                    9=>10, 10=>11,11=>12,12=>13,13=>14,14=>15,15=>16,16=>17,
                    default => null
                };
            }else{
                $match = match ($slot) {
                    0=> 1, 1=>2,2=>3,3=>4,4=>5,5=>6,6=>7,7=>8,
                    9=>10, 10=>11,11=>12,12=>13,13=>14,14=>15,15=>16,16=>17,
                    18=> 19, 19=>20,20=>21,21=>22,22=>23,23=>24,24=>25,25=>26,
                    27=>28,28=>29,29=>30,30=>31,31=>32,32=>33,33=>34,34=>35,
                    36=>37, 37=>38,38=>39,39=>40,40=>41,41=>42,42=>43,43=>44,
                    default => null
                };
            }
        }else if ($pos === 4)
        {
            if($type){
                $match = match ($slot) {
                    0 => 9, 1 => 10, 2 => 11, 3 => 12, 4 => 13, 5 => 14, 6 => 15, 7 => 16, 8 => 17,
                    default => null
                };
            }else{
                $match = match ($slot) {
                    0 => 9, 1 => 10, 2 => 11, 3 => 12, 4 => 13, 5 => 14, 6 => 15, 7 => 16, 8 => 17,
                    9 => 18, 10=> 19, 11=>20, 12 => 21, 13=>2, 14 => 23, 15=>24, 16=>25, 17=>26,
                    18 => 27, 19=> 28, 20=>29, 21 => 30, 22=>31, 23 => 32, 24=>33, 25=>34, 26=>35,
                    27 => 36, 28=> 37, 29=>38, 30 => 39, 31=>40, 32 => 41, 33=>42, 34=>43, 35=>44,
                    default => null
                };
            }
        }
        return $match;
    }
}