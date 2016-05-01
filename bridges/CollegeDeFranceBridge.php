<?php
class CollegeDeFranceBridge extends BridgeAbstract{

	public function loadMetadatas() {
		$this->maintainer = "pit-fgfjiudghdf";
		$this->name = "CollegeDeFrance";
		$this->uri = "http://www.college-de-france.fr/";
		$this->description = "Returns the latest audio and video from CollegeDeFrance";
		$this->update = "2016-05-01";
	}

	public function collectData(array $param) {
		$months = array(
			'01' => 'janv.',
			'02' => 'févr.',
			'03' => 'mars',
			'04' => 'avr.',
			'05' => 'mai',
			'06' => 'juin',
			'07' => 'juil.',
			'08' => 'août',
			'09' => 'sept.',
			'10' => 'oct.',
			'11' => 'nov.',
			'12' => 'déc.'
		);
		// The "API" used by the site returns a list of partial HTML in this form
		/* <li>
		 * 	<a href="/site/thomas-romer/guestlecturer-2016-04-15-14h30.htm" data-target="after">
		 * 		<span class="date"><span class="list-icon list-icon-video"></span><span class="list-icon list-icon-audio"></span>15 avr. 2016</span>
		 * 		<span class="lecturer">Christopher Hays</span>
		 * 		<span class='title'>Imagery of Divine Suckling in the Hebrew Bible and the Ancient Near East</span>
		 * 	</a>
		 * </li>
		 */
		$html = file_get_html('http://www.college-de-france.fr/components/search-audiovideo.jsp?fulltext=&siteid=1156951719600&lang=FR&type=all') or $this->returnError('Could not request CollegeDeFrance.', 404);
		foreach($html->find('a[data-target]') as $element) {
			$item = new \Item();
			$item->title = $element->find('.title', 0)->plaintext;
			// Most relative URLs contains an hour in addition to the date, so let's use it
			// <a href="/site/yann-lecun/course-2016-04-08-11h00.htm" data-target="after">
			// But unfortunately some don't
			// <a href="/site/institut-physique/The-Mysteries-of-Decoherence-Sebastien-Gleyzes-[Video-3-35].htm" data-target="after">
			$d = DateTime::createFromFormat('!Y-m-d-H\hi', substr($element->href, -20, -4)) ?: DateTime::createFromFormat('!H m Y', str_replace(array_values($months), array_keys($months), $element->find('.date', 0)->innertext));
			$item->timestamp = $d->format('U');
			$item->content =  $element->find('.lecturer', 0)->innertext . ' - ' . $element->find('.title', 0)->innertext;
			$item->uri = 'http://www.college-de-france.fr' . $element->href;
			$this->items[] = $item;
		}
	}

	public function getName(){
		return 'CollegeDeFrance';
	}

	public function getURI(){
		return 'http://www.college-de-france.fr/';
	}

	public function getCacheDuration(){
		return 3600*3; // 3 hour
	}
}
