<?php

namespace lokiPM\PluginCMD;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginManager;
use pocketmine\utils\Filesystem;

class Main extends PluginBase {

    public function onEnable(): void {
    }

    public function onDisable(): void {
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch ($command->getName()) {
            case "plenable":
                return $this->enablePlugin($sender, $args);
            case "pldisable":
                return $this->disablePlugin($sender, $args);
        }
        return false;
    }

    private function enablePlugin(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("plugincmd.use")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return false;
        }

        if (empty($args)) {
            $sender->sendMessage(TextFormat::RED . "Please specify a plugin to enable.");
            return false;
        }

        $pluginName = $args[0];
        $pluginManager = $this->getServer()->getPluginManager();
        $plugin = $pluginManager->getPlugin($pluginName);

        if ($plugin === null) {
            // Try to load the plugin if it exists in the plugin folder
            $pluginFolder = $this->getServer()->getDataPath() . "plugins/";
            $pluginFile = $pluginFolder . $pluginName . ".phar";

            if (file_exists($pluginFile)) {
                $pluginManager->loadPlugin($pluginFile);
                $plugin = $pluginManager->getPlugin($pluginName);

                if ($plugin !== null) {
                    try {
                        $pluginManager->enablePlugin($plugin);
                    } catch (\Exception $e) {
                        $sender->sendMessage(TextFormat::RED . "An error occurred while enabling the plugin.");
                    }
                    return true;
                }
            }
            $sender->sendMessage(TextFormat::RED . "§cPlugin not found.");
            return false;
        }

        if ($plugin->isEnabled()) {
            return false;  // No message needed if plugin is already enabled
        }

        try {
            $pluginManager->enablePlugin($plugin);
        } catch (\Exception $e) {
            $sender->sendMessage(TextFormat::RED . "An error occurred while enabling the plugin.");
        }

        return true;
    }

    private function disablePlugin(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("plugincmd.use")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return false;
        }

        if (empty($args)) {
            $sender->sendMessage(TextFormat::RED . "Please specify a plugin to disable.");
            return false;
        }

        $pluginName = $args[0];
        $pluginManager = $this->getServer()->getPluginManager();
        $plugin = $pluginManager->getPlugin($pluginName);

        if ($plugin === null) {
            $sender->sendMessage(TextFormat::RED . "§cPlugin not found.");
            return false;
        }

        if (!$plugin->isEnabled()) {
            return false;  // No message needed if plugin is already disabled
        }

        try {
            $pluginManager->disablePlugin($plugin);
        } catch (\Exception $e) {
            $sender->sendMessage(TextFormat::RED . "An error occurred while disabling the plugin.");
        }

        return true;
    }
}
