<?php

namespace App\Support;

class Alert
{
    public function success(string $message): self
    {
        return $this->flash('success', $message);
    }

    public function error(string $message): self
    {
        return $this->flash('error', $message);
    }

    public function warning(string $message): self
    {
        return $this->flash('warning', $message);
    }

    public function info(string $message): self
    {
        return $this->flash('info', $message);
    }

    protected function flash(string $key, string $message): self
    {
        session()->flash($key, $message);

        return $this;
    }
}
