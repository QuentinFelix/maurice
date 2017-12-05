<?php
/** Freesewing\Patterns\Beta\MauriceCap class */
namespace Freesewing\Patterns\Beta;

/**
 * A flatcap pattern by Quentin Felix
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class MauriceCap extends \Freesewing\Patterns\Core\Pattern
{

    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /* the repartition between top and side. defined as side/total circumference */
    const REPARTITION_CIRCUMFERENCE = 0.8;
    
    const BRIM_EXTRA = 0;

    /**
     * Sets up options and values for our draft
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {
        $this->setValueIfUnset('countertest', 0);
        $this->setValueIfUnset('coef', 1);
    }


    /*
        ____             __ _
       |  _ \ _ __ __ _ / _| |_
       | | | | '__/ _` | |_| __|
       | |_| | | | (_| |  _| |_
       |____/|_|  \__,_|_|  \__|

      The actual sampling/drafting of the pattern
    */

    /**
     * Generates a sample of the pattern
     *
     * Here, you create a sample of the pattern for a given model
     * and set of options. You should get a barebones pattern with only
     * what it takes to illustrate the effect of changes in
     * the sampled option or measurement.
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        $this->initialize($model);

        $this->msg('Tweaking cap to fit head circumference:');
        do {
            $this->draftSide($model);
            $this->draftTop($model);
            $this->headCircDelta($model) ;
        
            if ($this->headCircDelta($model)<0) $this->setValue('coef', $this->v('coef')*1.03);
            else $this->setValue('coef', $this->v('coef')*0.99);
        
            $this->setValue('countertest', $this->v('countertest') + 1);
            $this->msg(
                'Run '.str_pad($this->v('countertest'),2,' ',STR_PAD_LEFT).
                ': '.
                str_pad(round($this->headCircDelta($model),2),5,' ',STR_PAD_LEFT).
                'mm off (Coef = '.
                round($this->v('coef'),4).
                ')'
            ) ; 
        } while ($this->v('countertest') < 70 and abs($this->headCircDelta($model)) > 0.8);
        
        $this->draftBrimBottom($model);
        $this->draftBrimTop($model);
        $this->draftBrimPlastic($model);
    }

    /**
     * Generates a draft of the pattern
     *
     * Here, you create the full draft of this pattern for a given model
     * and set of options. You get a complete pattern with
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
	    // Continue from sample
        $this->sample($model);

        // Finalize parts
        $this->finalizeSide($model);
        $this->finalizeTop($model);
        
        $this->finalizeBrimBottom($model);
        $this->finalizeBrimTop($model);
        $this->finalizeBrimPlastic($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to our example part
           // $this->paperlessExamplePart($model);
        }
    }


    /**
     * Drafts the Top
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftTop($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['top'];
		
		$p->newPoint(1, 0, 0, 'Middle front');
		$p->newPoint(2, $this->v('coef')*202, 0, 'Middle start of curve');
		$p->addPoint(13, $p->shiftOutwards(1, 2, $this->v('coef')*48),'Middle start of curve handle');
		$p->newPoint(3, $this->v('coef')*388.5, $this->v('coef')*73.5, 'Middle back');
		$p->newPoint(400, 0, $this->v('coef')*40, 'Side front handle');
		$p->addPoint(4, $p->shift (1, $p->angle(2, 1)+90, $this->v('coef')*44));

		$p->newPoint(6, $this->v('coef')*150, $this->v('coef')*106, 'Side side point');
		$p->addPoint(5, $p->shift (6, $p->angle(2, 1), $this->v('coef')*110));
		$p->addPoint(7, $p->shift (6, $p->angle(1, 2), $this->v('coef')*60));
		$p->newPoint(9, $this->v('coef')*290, $this->v('coef')*80.8, 'Side inner curve point');
		$p->addPoint(8, $p->shift (9, $p->angle(2, 1), $this->v('coef')*20));
		$p->addPoint(10, $p->shift (9, $p->angle(1, 2), $this->v('coef')*20));	
		$p->newPoint(12, $this->v('coef')*342, $this->v('coef')*110, 'Top side point ');
		$p->addPoint(30, $p->shift (12, $p->angle(12, 3)+90, $this->v('coef')*15));
		$p->addPoint(31, $p->shift (3, $p->angle(12, 3)+90, $this->v('coef')*15));
		$p->addPoint(11, $p->shiftOutwards(12, 30, $this->v('coef')*8), 'handle');	
		$p->addPoint(14, $p->shiftOutwards(3, 31, $this->v('coef')*34), 'handle');	
		
		$p->addPoint(33, $p->shiftFractionTowards(2,31,0.52), 'construction point for middle');
		$p->addPoint(32, $p->shift(33, $p->angle(31, 2)-90, $this->v('coef')*13.5), 'middle point');
		
		$p->addPoint(34, $p->shift(32, $p->angle(33, 32)+90, $this->v('coef')*13), 'middle point');
		$p->addPoint(35, $p->shift(32, $p->angle(33, 32)-90, $this->v('coef')*32), 'middle point');
		
		$p->addPoint(43, $p->shiftFractionTowards(6,9,0.65), 'construction point for middle');
		$p->addPoint(42, $p->shift(43, $p->angle(9, 6)-90, -1.5*$this->v('coef')), 'middle point');
		
		$p->addPoint(44, $p->shift(42, $p->angle(43, 42)+90, $this->v('coef')*5), 'middle point');
		$p->addPoint(45, $p->shift(42, $p->angle(43, 42)-90, $this->v('coef')*5), 'middle point');
		
		
		$path = 'M 1 L 2 C  13 34 32 C 35 14 31 L 3 L 12 L 30 C 11 10 9 C 8 44 42 C 45 7 6 C 5 4 1 Z ';
		$p->newPath('seamline', $path, ['class' => 'fabric']);	
		$p->paths['seamline']->setSample(true);
		$this->setValue('topHeadCirc', 2*$p->distance(3,12));
		$p->newPoint('samplerAnchor', $p->x('2'),$p->y('2'));
    }
	public function draftSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];
		
		$p->newPoint(1, 0, 0);
		$p->newPoint(2, $this->v('coef')*75, 0 );
		$p->addPoint(3, $p->shift (1, $p->angle(2, 1)+90, $this->v('coef')*25), 'handle for 1');
		$p->addPoint(4, $p->shift (2, $p->angle(2, 1)+90, $this->v('coef')*50), 'handle for 2');
		$p->addPoint(5, $p->shiftOutwards(1, 2, $this->v('coef')*169.5), 'construction point for 6' );
		$p->addPoint(6, $p->shift (5, $p->angle(2, 1)+90, $this->v('coef')*123));

		$p->addPoint(8, $p->shift (6, $p->angle(2, 1)+87, $this->v('coef')*15));
		$p->addPoint(7, $p->shift (6, $p->angle(8, 6)+90, $this->v('coef')*40), 'handle for 6');
		$p->addPoint(9, $p->shift (8, $p->angle(6, 8), $this->v('coef')*60), 'handle for 8');
		$p->addPoint(10, $p->shiftOutwards(1, 2, $this->v('coef')*120), 'construction point for 11' );
		$p->addPoint(11, $p->shift (10, $p->angle(2, 1)+90, $this->v('coef')*200));
		$p->addPoint(12, $p->shift(11, $p->angle(1, 2), $this->v('coef')*20), 'handle 1 for 11');
		$p->addPoint(13, $p->shift(11, $p->angle(2, 1), $this->v('coef')*25), 'handle 2 for 11');
		
		// shifting the curve between 11 and 1
		$p->addPoint(14, $p->shiftFractionTowards(11,1,0.58), 'construction point for 15');
		$p->addPoint(15, $p->shift(14, $p->angle(1, 11)-90, $this->v('coef')*42.5), 'point between 1 and 11');
		
		$p->addPoint(16, $p->shift(15, $p->angle(14, 15)+90, $this->v('coef')*85), 'handle 1 for 14');
		$p->addPoint(17, $p->shift(15, $p->angle(16, 15),$this->v('coef')*45), 'handle 2 for 14');

		// shifting the curve between 2 and 6	
		$p->addPoint(18, $p->shiftFractionTowards(6,2,0.5), 'construction point for 19');
		$p->addPoint(19, $p->shift(18, $p->angle(2, 6)-90, $this->v('coef')*50), 'point between 2 and 6');
		
		$p->addPoint(20, $p->shift(19, $p->angle(18, 19)+90, $this->v('coef')*30), 'handle 1 for 18');
		$p->addPoint(21, $p->shift(19, $p->angle(18, 19)-90, $this->v('coef')*20), 'handle 1 for 18');		

		
		$path1 = 'M 2 C 4 21 19 C 20 7 6 L 8 C 9 12 11 C 13 16 15 C 17 3 1';
		$p->newPath('seamline1', $path1, ['class' => 'fabric']);
		$path2 = 'M 1 L 2';
		$p->newPath('seamline2', $path2, ['class' => 'fabric']);
		$this->setValue('sideHeadCirc',2* $p->curveLen(2, 4, 21, 19) + 2* $p->curveLen(19, 20, 7, 6));
		$p->paths['seamline1']->setSample(true);
		$p->paths['seamline2']->setSample(true);
    }

    protected function headCircDelta($model) 
    {
        $this->setValue('headCircActual', $this->v('sideHeadCirc') + $this->v('topHeadCirc'));
        return $this->v('headCircActual') - ($model->m('headCircumference') + $this->o('headEase'));
    }

	public function draftBrimBottom($model)
    {
        /** @var \Freesewing\Part $p */
		$p = $this->parts['brimBottom'];

		$p->newPoint(1, $this->v('coef')*-88, $this->v('coef')*-78);
		$p->addPoint(2, $p->shift(1, -65, $this->v('coef')*30), 'handle for inner border');
		$p->newPoint(4, 0, 0, 'middle of inner border');
		$p->addPoint(3, $p->shift(4, 180, $this->v('coef')*70), 'Handle for inner border middle');
		$p->addPoint(5,$p->flipX(3,$p->x(4)));
		$p->addPoint(6,$p->flipX(2,$p->x(4)));
		$p->addPoint(7,$p->flipX(1,$p->x(4)));
		
		$p->addPoint(8, $p->shift(1, -105, $this->v('coef')*118), 'Handle for outer border');
		$p->addPoint(10, $p->shift(4, -90, $this->v('coef')*58), 'top of the brim');
		$p->addPoint(9, $p->shift(10, 180, $this->v('coef')*40), 'Handle for top of the brim');
		
		$p->addPoint(11,$p->flipX(8,$p->x(4)));
		$p->addPoint(12,$p->flipX(9,$p->x(4)));
		
		$path = 'M 1 C 2 3 4 C 5 6 7 C 11 12 10 C 9 8 1  z';
		$p->newPath('seamline', $path, ['class' => 'fabric']);
		$p->paths['seamline']->setSample(true);
    }

	public function draftBrimTop($model)
    {	
		$p = $this->parts['brimTop'];
		 $this->clonePoints('brimBottom', 'brimTop');

		$pathinner = 'M 1 C 2 3 4 C 5 6 7';
		$p->newPath('seamline1', $pathinner, ['class' => 'fabric']);
		$p->paths['seamline1']->setRender(false);
		
		$pathouter = 'M 7 C 11 12 10 C 9 8 1';
		$p->newPath('seamline2', $pathouter, ['class' => 'hint']);
		$p->paths['seamline2']->setRender(false);
		

		$p->offsetPath('seamline45', 'seamline2', 3, false, ['class' => 'fabric']);

		$allpathstring = $pathinner;
		$addthis =  $p->paths['seamline45']->getPathstring();
		$addthis = substr ( $addthis , 2 , strlen( $addthis )-1);
		$allpathstring .=' L '. $addthis.' Z';

		$p->newPath('seamline10', $allpathstring, ['class' => 'fabric']);
		//$p->paths['seamline45']->setSample(true);
		//$p->paths['seamline10']->setSample(true);
    }
	
	public function draftBrimPlastic($model)
    {	
		$p = $this->parts['brimPlastic'];
		 $this->clonePoints('brimBottom', 'brimPlastic');
		$p->addPoint(501, $p->shiftAlong(1,2,3,4,3));
		$p->splitCurve(1,2,3,4,501,'s');	

		$p->addPoint(502,$p->flipX(501,$p->x(4)));
		$p->addPoint(503,$p->flipX('s7',$p->x(4)));
		$p->addPoint(504,$p->flipX('s6',$p->x(4)));

		$pathinner = 'M 501 C s7 s6 4 C 504 503 502';
		$p->newPath('seamline1', $pathinner, ['class' => 'hint']);
		$p->paths['seamline1']->setRender(false);		
		
		$p->addPoint(511, $p->shiftAlong(1,8,9,10,4));
		$p->addPoint(512,$p->flipX(511,$p->x(4)));
		$p->splitCurve(1,8,9,10,511,'t');
		$p->addPoint(513,$p->flipX('t7',$p->x(4)));
		$p->addPoint(514,$p->flipX('t6',$p->x(4)));
		$pathouter = 'M 512 C 513 514 10 C t6 t7 511';
		$p->newPath('seamline2', $pathouter, ['class' => 'hint']);
		$p->paths['seamline2']->setRender(false);

		$p->offsetPath('seamline60', 'seamline1', -1.5, true, ['class' => 'fabric']);
		$p->offsetPath('seamline65', 'seamline2', 1.5, true, ['class' => 'fabric']);		
		$p->newPath('seamline3', "M seamline60-startPoint L seamline65-endPoint z", ['class' => 'fabric']);
		$p->newPath('seamline4', "M seamline65-startPoint L seamline60-endPoint z", ['class' => 'fabric']);
		
    }

    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/seam-allowance/grainline and so on
    */
   public function finalizeTop($model)
    {
        /** @var Part $p */
        $p = $this->parts['top'];
		
		// Seam allowances
		if($this->o('sa')) {
			$p->offsetPath('sideSA', 'seamline', $this->o('sa'), 1, ['class' => 'fabric sa']);
		}
		
		// Grainline
        $p->newPoint('grainlineTop', 0.8*$p->x(1)+0.2*$p->x(2),$p->y(32));
        $p->newPoint('grainlineBottom',  0.2*$p->x(2)+ 0.8*$p->x(32), $p->y('grainlineTop'));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(6),  0.5*($p->y(grainlineTop) +$p->y(6)) , 'Title anchor');
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '2x main, 2x lining','small');

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',0, 50));
        $p->newSnippet('logo', 'logo', 'logoAnchor');
    }
	
	 public function finalizeSide($model)
    {
        /** @var Part $p */
        $p = $this->parts['side'];
		
		// Seam allowances
		if($this->o('sa')) {
			$p->offsetPath('path51', 'seamline1', $this->o('sa'), 1,['class' => 'fabric sa'] );
			$p->newPath('path52', 'M 2 L path51-startPoint',['class' => 'fabric sa'] );
			$p->newPath('path53', 'M 1 L path51-endPoint',['class' => 'fabric sa'] );
		}

        // Cut on fold
        $p->newPoint('cofTop', $p->x(1) + 10, $p->y(1) + 0, 'Cut on fold top');
        $p->newPoint('cofBottom', $p->x(2) - 10, $p->y('cofTop'), 'Cut on fold bottom');
        $p->newCutonfold('cofTop','cofBottom',  $this->t('Cut on fold'));

		// Grainline
		$p->newPoint('grainlineTop', 0.5*( $p->x(19)+ $p->x(15)),0.5*( $p->y(8)+$p->y(6)));
        $p->newPoint('grainlineBottom', $p->x(8)-10, $p->y('grainlineTop'));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(11),  0.5*($p->y(grainlineTop) +$p->y(11)) , 'Title anchor');
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x  main, 1x lining '.$this->t('Cut on fold'),'small');

        // Logo
        $p->addPoint('logoAnchor', $p->shiftFractionTowards(14,15, 0.5));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');

        // Scalebox
        $p->newPoint('scaleboxAnchor', $p->x(6)-55,$p->y(2));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
    }
	
	   public function finalizeBrimBottom($model)
    {
        /** @var Part $p */
        $p = $this->parts['brimBottom'];
		
		// Seam allowances
		if($this->o('sa')) {
				$p->offsetPath('path51', 'seamline', $this->o('sa'), 1,['class' => 'fabric sa'] );
		}
		
       // Grainline
        $p->newPoint('grainlineTop', $p->x(4), 0.05*$p->y(10)+ 0.95*$p->y(4));
        $p->newPoint('grainlineBottom',  $p->x(grainlineTop), 0.95*$p->y(10)+ 0.05*$p->y(4));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(4)-40,  0.6*$p->y(4) +0.4*$p->y(10) , 'Title anchor');
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '1x ','small');

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-5, 80));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
    }
	
	   public function finalizeBrimTop($model)
    {
        /** @var Part $p */
        $p = $this->parts['brimTop'];
		
		// Seam allowances
		if($this->o('sa')) {		
		$p->offsetPath('path51', 'seamline10', $this->o('sa'), 1,['class' => 'fabric sa'] );
		}
		
		// Grainline
        $p->newPoint('grainlineTop', $p->x(4), 0.05*$p->y(10)+ 0.95*$p->y(4));
        $p->newPoint('grainlineBottom',  $p->x(grainlineTop), 0.95*$p->y(10)+ 0.05*$p->y(4));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(4)-40,  0.6*$p->y(4) +0.4*$p->y(10) , 'Title anchor');
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '1x ','small');

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-5, 80));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
    }
	
		   public function finalizeBrimPlastic($model)
    {
        /** @var Part $p */
        $p = $this->parts['brimPlastic'];

        // Title
        $p->newPoint('titleAnchor', $p->x(4)-40,  0.6*$p->y(4) +0.4*$p->y(10) , 'Title anchor');
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '1x ','small');

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-5, 80));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
    }
	
    /*
        ____                       _
       |  _ \ __ _ _ __   ___ _ __| | ___  ___ ___
       | |_) / _` | '_ \ / _ \ '__| |/ _ \/ __/ __|
       |  __/ (_| | |_) |  __/ |  | |  __/\__ \__ \
       |_|   \__,_| .__/ \___|_|  |_|\___||___/___/
                  |_|

      Instructions for paperless patterns
    */

    /**
     * Adds paperless info for the example part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessExamplePart($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['examplePart'];
    }
}
