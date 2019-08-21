<?php

declare(strict_types=1);

namespace {

    /**
     * Class CompileScript
     */
    class CompileScript implements \proxy\plugin\ScriptInterface {

        public function load() {
            \proxy\Server::getInstance()->getCommandMap()->registerCommand(new class($this) extends \proxy\command\Command {

                public function __construct(CompileScript $plugin) {
                    parent::__construct("compile", "Compiles proxy to .phar");
                }

                public function onExecute(\proxy\command\CommandSender $sender, array $args): bool {
                    if(!$sender instanceof \proxy\command\ConsoleCommandSender) {
                        $sender->sendMessage("This command can be used only from console.");
                        return false;
                    }

                    if(!is_dir(getcwd() . DIRECTORY_SEPARATOR . "artifacts")) {
                        @mkdir(getcwd() . DIRECTORY_SEPARATOR . "artifacts");
                    }

                    $fileName = getcwd() . DIRECTORY_SEPARATOR . "artifacts" . DIRECTORY_SEPARATOR . "MCBEProxy-" . time() . ".phar";

                    $phar = new Phar($fileName);
                    $phar->setFileClass();
                    $phar->setDefaultStub('proxy/Bootstrap.php');
                    $phar->buildFromDirectory(getcwd());
                    $sender->sendMessage("Phar file generated!");
                    return true;
                }
            });
        }

        public function unload() {}

        /**
         * @return string
         */
        public function getName(): string {
            return "CompileScript";
        }

        /**
         * @return string
         */
        public function getDescription(): string {
            return "Compiles MCBEProxy to Phar";
        }

        /**
         * @return string
         */
        public function getVersion(): string {
            return "1.0.0";
        }
    }

    return new CompileScript();
}


