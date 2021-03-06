<?php
	$compTime = 10;				// time in seconds to use for 'computer' timing
	
	// Likert Settings
	//
	// Creates a row of radio buttons for the user to select. Only 1 button
	//     may be submitted as the response. Additional settings can be added
	//     using a "Settings" column, to control the values available in the
	//     scale.
	//
	//
	//
	// "Procedure Notes" column
	//
	//     In this column, you can multiple entries separated by the pipe ( | )
	//         character. The first entry contains the question, which appears
	//         at the top of the trial.
	//
	//     Additional entries may be added to put descriptions over the
	//         likert scale. If you have 2 more entries (e.g., 
	//         "question | Not at all | Very much"), then these will appear
	//         over the left and right sides of the scale. If you have 3
	//         additional entries, these will appear over the left, center,
	//         and right sides of the scale.
	//
	//     All entries may reference the $cue and $answer for that trial, by
	//         simply placing those keywords in the text. For example, a trial
	//         with "apple" as the cue could have "How much do you like: $cue"
	//         as the question, and this would show up as
	//         "How much do you like: apple". These can be used in the scale
	//         descriptors as well.
	//
	//
	//
	// "Settings" column (optional)
	//
	//     If you simply put an entry, such as '7' or 'f', the scale will 
	//         be from 1 to 7 (or "a" to "f").
	//
	//     If you put a range (2 entries separated by two colons '::'), the
	//         scale will start from the first entry, and end at the second.
	//         Both number ranges (-3::3) and alphabetical ranges (a::f)
	//         are fine. If the range is numeric, you can also define the
	//         step size using parenthesis after the range ("0::1(.1)").
	//
	//     You can also combine ranges using commas, such as "1::3,a::c".
	//         Using these, its possible to have duplicate entries, so
	//         something like "1,1,1,1::4" is possible. If you want to set
	//         the step size for numeric ranges, please enter the step size
	//         after each range, like "1::3(.1), a::g(2), 7::9(.1)"
	
	function trimExplode( $delimiter, $string ) {
		$output = array();
		$explode = explode( $delimiter, $string );
		foreach( $explode as $explosion ) {
			$output[] = trim($explosion);
		}
		return $output;
	}
	
	function createRange( $endValue ) {
		$output = array();
		$step = findRangeStep( $endValue );
		if( is_numeric($endValue) ) {
			$output = range(1, $endValue, $step);
		} elseif( strtolower($endValue) === $endValue ) {
			$output = range('a', $endValue, $step);
		} else {
			$output = range('A', $endValue, $step);
		}
		return $output;
	}
	
	function findRangeStep( &$string ) {
		if( strpos($string, '(') !== FALSE ) {
			$step = substr($string, strpos($string, '('));
			$step = filter_var($step, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$string = substr($string, 0, strpos($string, '('));
		} else {
			$step = 1;
		}
		return $step;
	}
	
	if( !isset( $procedureNotes ) ) $procedureNotes = '';
	$allTextsAsString = $text;
	$likertTexts = trimExplode( '|', $allTextsAsString );
	
	$likertQuestion = array_shift( $likertTexts );
	if( count($likertTexts) < 2 ) {
		$likertDescrips = array_pad( $likertTexts, 3, '' );
	} elseif( count($likertTexts) === 2 ) {
		$likertDescrips = array();
		$likertDescrips[] = $likertTexts[0];
		$likertDescrips[] = '';
		$likertDescrips[] = $likertTexts[1];
	} else {
		$likertDescrips = $likertTexts;
	}
    
    $text = $likertQuestion . ' | ' . implode(' | ', $likertDescrips);
	
	if( !isset( $settings ) ) $settings = '';
	
	if( $settings === '' ) {
		$likertScale = range(1,7);
	} else {
		$likertScale = array();
		if(     strpos($settings, ',' ) === FALSE 
			AND strpos($settings, '::') === FALSE
		) {
			$likertScale = createRange($settings);
		} else {
			$ranges = trimExplode(',', $settings);
			foreach( $ranges as $range ) {
				if( strpos($range, '::') === FALSE ) {
					$likertScale[] = $range;
				} else {
					$endPoints = trimExplode('::', $range);
					$first = array_shift($endPoints);
					$last  = array_pop($endPoints);
					$step  = findRangeStep( $last );
					$newRange = range($first, $last, $step);
					$likertScale = array_merge($likertScale, $newRange);
				}
			}
		}
	}
	$width = 100/count($likertScale);
?>
<style>
	.LikertArea, .LikertScale, .LikertOptions	{	width: 100%;	}
	.LikertArea	{	text-align: center;	}
	.LikertQuestion	{	font-size: 120%;	margin-bottom:	10px;	}
	.LikertDescriptions	{	white-space: nowrap;	color: grey;	width: 90%;	margin: auto;	}
	.LikertDescriptions > div	{	width: 33.3%;	display: inline-block;	white-space: normal;	}
	.LikertLeftDesc	{	text-align: left;	}
	.LikertRightDesc	{	text-align: right;	}
	.LikertOptions	{	table-layout: fixed;	}
	.LikertOptions td	{	width: <?= $width ?>%;	}
	.LikertOptions label	{	display: block !important;	margin: 0 !important;	}
	.LikertArea input	{	margin: 1px !important;	border: none !important;	box-shadow: none !important;	}
</style>
<div class="LikertArea">
	<div class="LikertQuestion"><?= $likertQuestion ?></div>
	<div class="LikertScale">
		<div class="LikertDescriptions"><?php
			?><div class="LikertLeftDesc"  ><?= $likertDescrips[0] ?></div><?php
			?><div class="LikertCenterDesc"><?= $likertDescrips[1] ?></div><?php
			?><div class="LikertRightDesc" ><?= $likertDescrips[2] ?></div>
		</div>
		<table class="LikertOptions"><tr><?php
			foreach( $likertScale as $value ) {
				echo '<td><label><input type="radio" name="Response" value="'.$value.'" /><br>'.$value.'</label></td>';
			}
		?></tr></table>
	</div>
</div>

<div class=textcenter>
	<input class="button button-trial-advance" id=FormSubmitButton type=submit value="Submit"   />
</div>