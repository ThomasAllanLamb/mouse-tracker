<?php

  class Route {

    //regular expression inferred from input $path which will only match the intended URLs
    private $regex;
    //given path with parameters encoded in :parameter form
    private $path;
    //array of parameter names. inferred from $path
    private $parameters;
    private $f;
    private $requestMethod;

    public function __construct ($path, $requestMethod, $f) {
      $path = trim($path, "/");
      $pathArgs = explode("/", $path);
      //pathArgs is an array of the path elements that came in. None of them contain slashes.

      //infer regex and parameters from path
      $parameters = [];
      $regex = "/^";
      for ($i = 0; $i <= count($pathArgs)-1; $i++) {
        $arg = $pathArgs[$i];
        if (substr($arg, 0, 1) === ":")
        {
          $parameters[] = substr($arg, 1);
          $regex = $regex."([^\\\]*)";
        }
        else
        {
          $regex = $regex.$arg;
        }
        //add delimiting slashes
        if ($i <= count($pathArgs)-2) {
          $regex = $regex."\/";
        }
      }
      $regex = $regex."$/";
      $this->regex = $regex;
      $this->parameters = $parameters;

      $this->path = path;

      $this->f = $f;

      $this->requestMethod = $requestMethod;
    }

    public function isMatch ($path, $requestMethod) {
      if ($this->requestMethod === $requestMethod && preg_match($this->regex, $path)) {
        return true;
      }
      else
      {
        return false;
      }
    }

    public function parameters ($path) {
      $parameters = [];
      $matches = [];
      if (preg_match($this->regex, $path, $matches)) {
        //first arg in matches is matches[1]
        for ($i = 0; $i <= count($matches)-2; $i++) {
          $key = $this->parameters[$i];
          $value = $matches[$i+1];
          $parameters[$key] = $value;
        }
        return $parameters;
      }
      else
      {
        throw new Error("Path '".$path."' does not match route '".$this->path."'.");
      }
    }

    public function applyPath ($path) {
      $this->applyParameters($this->parameters($path));
    }

    public function applyParameters ($parameters) {
      call_user_func($this->f, $parameters);
    }
  }