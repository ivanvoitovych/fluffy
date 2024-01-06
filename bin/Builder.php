<?php

class Builder
{
    private string $name;
    private array $vars;
    private array $steps;
    private array $actions;
    private string $environment;

    public function __construct(private string $baseDir, private array $inputs, private array $config)
    {
        $this->name = $config['name'] ?? 'default';
    }

    public function build()
    {
        if (count($this->inputs) == 0) {
            die('[Build] Too few parameters, expected prod, dev, local or your custom.' . PHP_EOL);
        }
        $this->environment = $this->inputs[0];
        echo "[Build] running build for '$this->environment'.." . PHP_EOL;

        if (!isset($this->config['environments'][$this->environment])) {
            die("[Build] Environment '$this->environment' is missing from the config." . PHP_EOL);
            if (!isset($this->config['environments'][$this->environment]['vars'])) {
                die("[Build] Variables are not defined for environment '$this->environment'." . PHP_EOL);
            }
            if (!isset($this->config['environments'][$this->environment]['steps'])) {
                die("[Build] Build steps are not defined for environment '$this->environment'." . PHP_EOL);
            }
        }
        if (!isset($this->config['actions'])) {
            die("[Build] Actions are not defined." . PHP_EOL);
        }
        $this->vars = $this->config['environments'][$this->environment]['vars'];
        $this->steps = $this->config['environments'][$this->environment]['steps'];
        $this->actions = $this->config['actions'];
        // validate steps
        foreach ($this->steps as $step) {
            if (is_callable($step)) {
                ($step)($this);
            } else {
                if (!isset($this->actions[$step])) {
                    die("[Build] Action '$step' is not defined." . PHP_EOL);
                }
                ($this->actions[$step])($this);
            }
        }
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getVariables(): array
    {
        return $this->vars;
    }

    public function setVariables(array $vars): void
    {
        $this->vars = $vars;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
