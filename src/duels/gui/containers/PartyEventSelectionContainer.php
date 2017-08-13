<?php

/**
 * Duels_v1-Alpha – PartyEventSelectionContainer.php
 *
 * Copyright (C) 2017 Jack Noordhuis
 *
 * This is private software, you cannot redistribute and/or modify it in any way
 * unless given explicit permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author Jack Noordhuis
 *
 * Created on 28/7/17 at 10:24 PM
 *
 */

namespace duels\gui\containers;

use core\CorePlayer;
use core\gui\container\ChestGUI;
use core\gui\item\GUIItem;
use core\Utils;
use duels\duel\Duel;
use duels\gui\item\kit\KitGUIItem;
use duels\gui\item\party\PartyTypeSelectionItem;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\inventory\BaseInventory;
use pocketmine\item\Item;
use pocketmine\item\MobHead;
use pocketmine\network\protocol\ContainerClosePacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PartyEventSelectionContainer extends ChestGUI {

	/** @var GUIItem[] */
	protected $defaultContents = [];

	public function __construct(CorePlayer $owner) {
		parent::__construct($owner);
		$this->defaultContents[] = new PartyTypeSelectionItem(Item::get(Item::MOB_HEAD, MobHead::STEVE, 1), Duel::TYPE_1V1, $this, "&l&b1v1");
		$this->defaultContents[] = new PartyTypeSelectionItem(Item::get(Item::MOB_HEAD, MobHead::STEVE, 2), Duel::TYPE_2V2, $this, "&l&b2v2");
		$this->defaultContents[] = Item::get(Item::AIR);
		$this->defaultContents[] = new PartyTypeSelectionItem(Item::get(Item::MOB_HEAD, MobHead::DRAGON, 1), Duel::TYPE_FFA, $this, "&l&bFFA");
		$this->setContents($this->defaultContents);
	}

	public function onOpen(Player $who) {
		$this->setContents($this->defaultContents);
		parent::onOpen($who);
	}

	public function close(Player $who) {
		$pk = new ContainerClosePacket();
		$pk->windowid = $who->getWindowId($this);
		$who->directDataPacket($pk);
		BaseInventory::onClose($who);
	}

	public function onSelect($slot, GUIItem $item, CorePlayer $player) {
		if(!$item instanceof PartyTypeSelectionItem) {
			throw new \InvalidArgumentException("Expected duels/gui/item/party/PartyTypeSelectionItem, got " . gettype($item) . " instead");
		}
		$player->removeWindow($this);
		$plugin = Main::getInstance();
		/** @var $session PlayerSession */
		if(!($session = $plugin->sessionManager->get($player->getName())) instanceof PlayerSession) {
			$player->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
			return false;
		}
		if($session->inParty()) {
			$type = $item->getDuelType();
			switch($type) {
				case Duel::TYPE_1V1:
					if(count($session->getParty()->getPlayers()) === 2) {
						$player->sendMessage(Utils::translateColors("&aStarting party 1v1 event..."));
						$session->lastSelectedPartyType = $item->getDuelType();
						$player->addWindow($player->getGuiContainer(Main::GUI_PARTY_KIT_SELECTION_CONTAINER));
					} else {
						$player->sendMessage(Utils::translateColors("&cThere must be two players in the party to start a 1v1 event!"));
					}
					return false;
				case Duel::TYPE_2V2:
					if(count($session->getParty()->getPlayers()) === 4) {
						$player->sendMessage(Utils::translateColors("&aStarting party 2v2 event..."));
						$session->lastSelectedPartyType = $item->getDuelType();
						$player->addWindow($player->getGuiContainer(Main::GUI_PARTY_KIT_SELECTION_CONTAINER));
					} else {
						$player->sendMessage(Utils::translateColors("&cThere must be four players in the party to start a 2v2 event!"));
					}
					return false;
				case Duel::TYPE_FFA:
					$player->sendMessage(Utils::translateColors("&aStarting party FFA event..."));
					$session->lastSelectedPartyType = $item->getDuelType();
					$player->addWindow($player->getGuiContainer(Main::GUI_PARTY_KIT_SELECTION_CONTAINER));
					return false;
				default:
					$player->sendMessage(Utils::translateColors("&cCould not start party event!"));
					return false;
			}
		} else {
			$player->sendMessage(Utils::translateColors("&cYou must be in a party to start an event!"));
			return false;
		}

		return false; // don't remove the item
	}

}