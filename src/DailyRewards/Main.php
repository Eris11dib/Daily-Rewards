<?php

/*
*
* Don't touch this file if you don't know PHP
* Author: Eris11dib
* GitHub Repo: https://github.com/Eris11dib/Daily-Rewards
*
*/
namespace DailyRewards;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\item\Item;

class Main extends PluginBase implements Listener{
	
	public $prefix = §l§7[§r§aDailyRewards§l§§7]§r
	public $players;
	public $items;
	public $config;
	const TIME = 86400;
	
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("§l§6Activated");
		$this->players = new Config($this->getDatafolder() . "players.yml", Config::YAML, []);
		$this->items = new Config($this->getDataFolder() . "items.yml", Config::YAML,[
		"Items" => ["306:0:1", "307:0:1", "308:0:1", "309:0:1"]
		]);
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML,[
		"first_reward_claim_message" => "Hai ricevuto il tuo primo premio giornaliero, torna domani!",
		"success_reward_claim_message" => "Hai ricevuto il tuo premio giornaliero,torna domani!",
		"waiting_reward_message" => "Devi aspettare il giorno seguente!",
		"error_reward_message" => "C'è stato un errore",
		]);
		
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label,array $args) : bool{
		switch(strtolower($cmd->getName())){
			case "dailyrewards":
			if($sender instanceof Player){
				$playerName = $sender->getName();
				if($this->players->exists($playerName)){
					if($this->players->get($playerName) - time() >= self::TIME){
						$item = $this->items->getNested("Items");
						foreach($item as $itemStr){
							$inv = $sender->getInventory();
							$expl = explode(":", $itemStr);
							$inv->addItem(Item::get( (int) $expl[0], (int) $expl[1], (int) $expl[2]));
							$sender->sendMessage($this->prefix . "§a" . $this->config->get("success_reward_claim_message"));
							
					   	}
					  }else{
					  	$sender->sendMessage($this->prefix . "§4" . $this->config->get("waiting_reward_message"));
				  }
			  }else{
			  	foreach($item as $itemStr){
					$inv = $sender->getInventory();
					$expl = explode(":", $itemStr);
			  	$inv->addItem(Item::get( (int) $expl[0], (int) $expl[1], (int) $expl[2]));
					  	$sender->sendMessage($this->prefix . "§a" . $this->config->get("first_reward_claim_message"));
			  }
			}
			  $this->players->set($playerName,time());
			}
		}
	}
}
