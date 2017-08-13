<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 9:30 PM
 */

namespace duels\gui\item\duel;

use core\CorePlayer;
use core\gui\item\GUIItem;
use core\language\LanguageUtils;
use duels\Main;
use pocketmine\item\Item;

class DuelKitRequestSelector extends GUIItem {

	public function __construct($parent = null) {
		parent::__construct(Item::get(Item::STICK, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&aDuel Stick"));
	}

	public function onClick(CorePlayer $player) {
		$player->addWindow($player->getGuiContainer(Main::GUI_DUEL_SELECTION_CONTAINER));
	}

	public function getPreviewDescription(CorePlayer $player) : string {
		return LanguageUtils::translateColors("&o&7Tap the item on a player to use.");
	}

	public function getCooldown() : int {
		return 5; // in seconds
	}

}