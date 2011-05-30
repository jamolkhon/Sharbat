<?php

class AnnotationParser
{
	public function hasAnnotation($docComment, $annotation)
	{
		return in_array($annotation, $this->getAnnotations($docComment));
	}

	public function getAnnotations($docComment)
	{
		$annotations = array();
		$tagLines = $this->getTagLines($docComment);

		foreach ($tagLines as $tagLine) {
			$tagInfo = preg_split('/\s+/', $tagLine);

			switch ($tagInfo[0]) {
			case '@param':
			case '@return':
				break;
			default:
				$annotations[] = substr($tagInfo[0], 1);
				break;
			}
		}

		return $annotations;
	}

	protected function getTagLines($docComment)
	{
		$tagLines = array();
		$commentLines = preg_split('/[\r\n]+/', $docComment);

		foreach ($commentLines as $line) {
			$line = trim($line);
			$line = trim($line, '*/');
			$line = trim($line);

			if ($line && $line[0] === '@') {
				$tagLines[] = $line;
			}
		}

		return $tagLines;
	}
}
