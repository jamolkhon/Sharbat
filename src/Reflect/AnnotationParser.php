<?php

namespace Sharbat\Reflect;

class AnnotationParser {

  public function parseAnnotations($docString) {
    $annotations = array();
    $annotationLines = $this->getPossibleAnnotationLines($docString);

    foreach ($annotationLines as $line) {
      preg_match_all('/^((?:\\S+?)?@[^\\s(]+)(?:\((.*?)\))?$/', $line, $matches,
        PREG_SET_ORDER);

      if (isset($matches[0])) {
        $name = str_replace('@', '', $matches[0][1]);

        if (isset($matches[0][2])) {
          $annotations[$name] = $this->getArguments($matches[0][2]);
        } else {
          $annotations[$name] = array();
        }
      }
    }

    return $annotations;
  }

  private function getPossibleAnnotationLines($docString) {
    $lines = preg_split('/[\r\n]+/', $docString);
    $annotationLines = array();

    foreach ($lines as $line) {
      $line = trim($line);
      $line = trim($line, '*/');
      $line = trim($line);

      if ($line && strpos($line, '@') !== false) {
        $annotationLines[] = $line;
      }
    }

    return $annotationLines;
  }

  private function getArguments($argumentListStr) {
    $tokens = $this->tokenizeArgumentList($argumentListStr);
    $array = '';

    foreach ($tokens as $token) {
      $token = trim($token);

      switch ($token) {
        case '[':
          $array .= 'array(';
          break;
        case ']':
          $array .= ')';
          break;
        case ',';
          $array .= ',';
          break;
        default:
          if ($token[0] === '"') {
            $array .= $token;
          } else if (substr($token, -1) === '=') {
            $array .= sprintf('"%s"=>', trim(substr($token, 0, -1)));
          } else {
            $array .= $this->safeValue($token);
          }
          break;
      }
    }

    $array = sprintf('return array(%s);', $array);
    return eval($array);
  }

  private function tokenizeArgumentList($argumentListStr) {
    $pattern = '/
      \s*[a-z_]\w*\s*=
      |
      \s*\[\s*
      |
      \s*\]\s*
      |
      \s*,\s*
      |
      \s*"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"\s*
      |
      \s*[^,\[\]]+
      /ix';
    preg_match_all($pattern, $argumentListStr, $matches);
    return isset($matches[0]) ? $matches[0] : array();
  }

  private function safeValue($str) {
    if (is_numeric($str) || in_array($str, array('true', 'false', 'null'))) {
      return $str;
    }

    return '"' . $str . '"';
  }

}
