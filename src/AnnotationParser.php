<?php

class AnnotationParser {

	public function hasAnnotation($docComment, $name) {
		$annotations = $this->getAnnotations($docComment);
		return isset($annotations[$name]);
	}
	
	public function getAnnotation($docComment, $name) {
		$annotationsAssoc = getAnnotationsAssoc($docComment);
		
		if (isset($annoationsAssoc[$name])) {
			return $annoationsAssoc[$name];
		}
		
		return null;
	}
	
	public function getAnnotations($docComment) {
		return array_values(getAnnotationsAssoc($docComment));
	}

	public function getAnnotationsAssoc($docComment) {
		$annotations = array();
		$tagLines = $this->getTagLines($docComment);

		foreach ($tagLines as $tagLine) {
			preg_match_all('|^@([a-zA-Z]+[a-zA-Z0-9_]*)(?:\(([a-zA-Z]+[a-zA-Z0-9_]*)\))?$|',
					$tagLine, $tagInfo, PREG_SET_ORDER);

			if (!empty($tagInfo)) {
				$arguments = array();
				
				if (!empty($tagInfo[0][2])) {
					$arguments[] = $tagInfo[0][2];
				}
				
				$annotations[$tagInfo[0][1]] = new Annotation($tagInfo[0][1], $arguments);
			}
		}

		return $annotations;
	}

	private function getTagLines($docComment) {
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
