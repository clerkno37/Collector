<?php
    /**
     * This is an example of a trial type in the Experiment/ folder overwriting
     * the trial type found in the Code/ folder.  Any changes to this Instruct
     * will be used throughout the program, so if you make any modifications,
     * such as increasing font-size, you can still download the latest Code/
     * folder of the Collector without worrying about accidentally losing
     * your changes.
     */
     
	$compTime = 5;        // time in seconds to use for 'computer' timing
?>
    <div><?php echo $text; ?></div>

	<!-- include form to collect RT and advance page -->
	<div class="precache textright">
		<input class="button button-trial-advance" id="FormSubmitButton" type="submit" value="Next" />
	</div>