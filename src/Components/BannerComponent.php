<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Banner\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-banner
 */

namespace SilverWare\Banner\Components;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\SS_List;
use SilverWare\Components\BaseComponent;
use SilverWare\Extensions\Model\ImageResizeExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\ToggleGroup;
use SilverWare\Model\Slide;

/**
 * An extension of the base component for a banner component.
 *
 * @package SilverWare\Banner\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2018 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-banner
 */
class BannerComponent extends BaseComponent
{
    /**
     * Define sort constants.
     */
    const SORT_ORDER  = 'order';
    const SORT_RANDOM = 'random';
    
    /**
     * Define animation constants.
     */
    const ANIM_SCROLL_LEFT  = 'scroll-left';
    const ANIM_SCROLL_RIGHT = 'scroll-right';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Banner Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Banner Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which shows a banner of images';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/banner: admin/client/dist/images/icons/BannerComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_BannerComponent';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = Slide::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'SortBy' => 'Varchar(16)',
        'Animate' => 'Boolean',
        'AnimationType' => 'Varchar(16)',
        'AnimationDuration' => 'Int',
        'NumberOfSlides' => 'AbsoluteInt'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'SortBy' => self::SORT_ORDER,
        'Animate' => 0,
        'AnimationDuration' => 20,
        'HideTitle' => 1
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        Slide::class
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'WrapperAttributesHTML' => 'HTMLFragment'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ImageResizeExtension::class
    ];
    
    /**
     * Defines the asset folder for uploading images.
     *
     * @var string
     * @config
     */
    private static $asset_folder = 'Slides/Banner';
    
    /**
     * Holds a list of slides which override the child slides.
     *
     * @var SS_List
     */
    protected $slides;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNSELECT', 'Select');
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'BannerOptions',
                $this->fieldLabel('BannerOptions'),
                [
                    TextField::create(
                        'NumberOfSlides',
                        $this->fieldLabel('NumberOfSlides')
                    ),
                    DropdownField::create(
                        'SortBy',
                        $this->fieldLabel('SortBy'),
                        $this->getSortByOptions()
                    ),
                    ToggleGroup::create(
                        'Animate',
                        $this->fieldLabel('Animate'),
                        [
                            DropdownField::create(
                                'AnimationType',
                                $this->fieldLabel('AnimationType'),
                                $this->getAnimationTypeOptions()
                            )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                            NumericField::create(
                                'AnimationDuration',
                                $this->fieldLabel('AnimationDuration')
                            )->setRightTitle(
                                _t(
                                    __CLASS__ . '.ANIMATIONDURATIONINSECONDS',
                                    'Duration of the animation cycle in seconds.'
                                )
                            )
                        ]
                    )
                ]
            )
        );
        
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['SortBy'] = _t(__CLASS__ . '.SORTBY', 'Sort by');
        $labels['Animate'] = _t(__CLASS__ . '.ANIMATE', 'Animate');
        $labels['BannerOptions'] = _t(__CLASS__ . '.BANNER', 'Banner');
        $labels['NumberOfSlides'] = _t(__CLASS__ . '.NUMBEROFSLIDES', 'Number of slides');
        $labels['AnimationType'] = _t(__CLASS__ . '.ANIMATIONTYPE', 'Animation type');
        $labels['AnimationDuration'] = _t(__CLASS__ . '.ANIMATIONDURATION', 'Animation duration');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the asset folder used by the receiver.
     *
     * @return string
     */
    public function getAssetFolder()
    {
        return $this->config()->asset_folder;
    }
    
    /**
     * Answers an array of class names for the HTML template.
     *
     * @return array
     */
    public function getClassNames()
    {
        $classes = parent::getClassNames();
        
        if ($this->Animate) {
            $classes[] = 'animated';
        }
        
        return $classes;
    }
    
    /**
     * Answers an array of HTML tag attributes for the wrapper.
     *
     * @return array
     */
    public function getWrapperAttributes()
    {
        $attributes = [
            'id' => $this->WrapperID,
            'class' => $this->WrapperClass
        ];
        
        if ($this->Animate && $this->AnimationDuration) {
            $attributes['style'] = "animation-duration: {$this->AnimationDuration}s";
        }
        
        $this->extend('updateWrapperAttributes', $attributes);
        
        return $attributes;
    }
    
    /**
     * Answers the HTML tag attributes for the wrapper as a string.
     *
     * @return string
     */
    public function getWrapperAttributesHTML()
    {
        return $this->getAttributesHTML($this->getWrapperAttributes());
    }
    
    /**
     * Answers an array of wrapper class names for the HTML template.
     *
     * @return array
     */
    public function getWrapperClassNames()
    {
        $classes = ['wrapper'];
        
        if ($this->Animate) {
            
            $classes[] = 'animated';
            
            if ($this->AnimationType) {
                $classes[] = $this->getAnimationTypeClass();
            }
            
        }
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers a unique ID for the wrapper element.
     *
     * @return string
     */
    public function getWrapperID()
    {
        return sprintf('%s_Wrapper', $this->getHTMLID());
    }
    
    /**
     * Answers a unique CSS ID for the wrapper element.
     *
     * @return string
     */
    public function getWrapperCSSID()
    {
        return $this->getCSSID($this->getWrapperID());
    }
    
    /**
     * Defines the slides property for the receiver.
     *
     * @param SS_List $slides
     *
     * @return $this
     */
    public function setSlides(SS_List $slides)
    {
        $list = ArrayList::create();
        
        foreach ($slides as $slide) {
            $list->push($slide->setParentInstance($this));
        }
        
        $this->slides = $list;
    }
    
    /**
     * Answers a list of all slides within the receiver.
     *
     * @return DataList
     */
    public function getSlides()
    {
        return $this->slides ?: $this->getAllChildrenByClass(Slide::class);
    }
    
    /**
     * Answers true if the receiver has at least one slide.
     *
     * @return boolean
     */
    public function hasSlides()
    {
        return (boolean) $this->getSlides()->exists();
    }
    
    /**
     * Answers a list of the enabled slides within the receiver.
     *
     * @return ArrayList
     */
    public function getEnabledSlides()
    {
        $slides = ArrayList::create();
        
        foreach ($this->getSlides() as $slide) {
            $slides->merge($slide->getEnabledSlides());
        }
        
        $slides = $this->sort($slides);
        
        if ($this->NumberOfSlides) {
            return $slides->limit($this->NumberOfSlides);
        }
        
        return $slides;
    }
    
    /**
     * Answers the CSS class for the selected animation type.
     *
     * @return string
     */
    public function getAnimationTypeClass()
    {
        switch ($this->AnimationType) {
            case self::ANIM_SCROLL_LEFT:
                return 'scroll-left';
            case self::ANIM_SCROLL_RIGHT:
                return 'scroll-right';
        }
    }
    
    /**
     * Answers an array of options for the animation type field.
     *
     * @return array
     */
    public function getAnimationTypeOptions()
    {
        return [
            self::ANIM_SCROLL_LEFT  => _t(__CLASS__ . '.SCROLLLEFT', 'Scroll Left'),
            self::ANIM_SCROLL_RIGHT => _t(__CLASS__ . '.SCROLLRIGHT', 'Scroll Right')
        ];
    }
    
    /**
     * Answers an array of options for the sort by field.
     *
     * @return array
     */
    public function getSortByOptions()
    {
        return [
            self::SORT_ORDER  => _t(__CLASS__ . '.ORDER', 'Order'),
            self::SORT_RANDOM => _t(__CLASS__ . '.RANDOM', 'Random')
        ];
    }
    
    /**
     * Sorts the given list of slides.
     *
     * @param SS_List $list
     *
     * @return SS_List
     */
    protected function sort(SS_List $list)
    {
        switch ($this->SortBy) {
            
            // Random Sort Order:
            
            case self::SORT_RANDOM:
                
                $slides = $list->toArray();
                
                shuffle($slides);
                
                return ArrayList::create($slides);
                
            // Default Sort Order:
                
            default:
                
                return $list;
                
        }
    }
}
