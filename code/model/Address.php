<?php
/**
 * Address model using a generic format for storing international addresses.
 * 
 * Typical Address Hierarcy:
 * 	Continent
 * 	Country
 * 	State / Province / Territory (Island?)
 * 	District / Suburb / County / City
 *		Code / Zip (may cross over the above)
 * 	Street / Road - name + type: eg Gandalf Cresent
 * 	(Premises/Building/Unit/Suite)
 * 		(Floor/Level/Side/Wing)
 * 	Number / Entrance / Room
 * 	Person(s), Company, Department
 *
 * Collection of international address formats:
 * @see http://bitboost.com/ref/international-address-formats.html
 * xAL address standard:
 * @see https://www.oasis-open.org/committees/ciq/ciq.html#6
 * Universal Postal Union addressing standards:
 * @see http://www.upu.int/nc/en/activities/addressing/standards.html
 */
class Address extends DataObject{

	private static $db = array(
		'Country'		=> 'ShopCountry',  //level1: Country = ISO 2-character country code
		'State'			=> 'Varchar(100)', //level2: Locality, Administrative Area, State, Province, Region, Territory, Island
		'City'			=> 'Varchar(100)', //level3: Dependent Locality, City, Suburb, County, District
		'PostalCode' 	=> 'Varchar(20)',  //code: ZipCode, PostCode (could cross above levels within a country)
		
		'Address'		=> 'Varchar(255)', //Number + type of thoroughfare/street. P.O. box
		'AddressLine2'	=> 'Varchar(255)', //Premises, Apartment, Building. Suite, Unit, Floor, Level, Side, Wing.

		'Latitude'		=> 'Float(10,6)',  //GPS co-ordinates
		'Longitude'		=> 'Float(10,6)',
		
		'Company'		=> 'Varchar(100)', //Business, Organisation, Group, Institution. 
		
		'FirstName'		=> 'Varchar(100)', //Individual, Person, Contact, Attention
		'Surname'		=> 'Varchar(100)',
		'Phone'			=> 'Varchar(100)',
	);
	
	private static $has_one = array(
		'Member' => 'Member'		
	);
	
	private static $casting = array(
		'Country' => 'ShopCountry'	
	);

	private static $required_fields = array(
		//'Country',
		'State',
		'City',
		'Address',
		'Phone'
	);

	function getFrontEndFields($params = null){
		$fields = FieldList::create(
			LiteralField::create('Anchor',''),
			CompositeField::create(
				CompositeField::create(
					$this->getCountryField(),
					$cityfield = TextField::create('City','')
						->addPlaceHolder(_t('Address.CITY','City'))
						->setAttribute('data-minlength','2')
						->setAttribute('maxlength','30')
						->prependText('<span class="icon icon-envelope"></span>')
						->addExtraClass('city locality')
						->setAttribute('x-autocompletetype','city'),
					$statefield = TextField::create('State','')
						->addPlaceHolder(_t('Address.STATE','State'))
						->setAttribute('data-minlength','2')
						->setAttribute('maxlength','30')
						->prependText('<span class="icon icon-envelope"></span>')
						->addExtraClass('state province region administrative-area')
						->setAttribute('x-autocompletetype','administrative-area')
				)->addExtraClass('pull-left'),
				CompositeField::create(
					$addressfield = TextField::create('Address','')
						->addPlaceHolder(_t('Address.ADDRESS','ex. Road 500'))
						->setAttribute('data-minlength','2')
						->setAttribute('maxlength','30')
						->prependText('<span class="icon icon-envelope"></span>')
						->addExtraClass('street-address address-line1')
						->setAttribute('x-autocompletetype','street-address'),
					$address2field = TextField::create('AddressLine2','')
						->addPlaceHolder(_t('Address.ADDRESSLINE2','ex. h.1024, ap.50'))
						->setAttribute('maxlength','30')
						->prependText('<span class="icon icon-envelope"></span>')
						->addExtraClass('address-line2 address-line3')
						->setAttribute('x-autocompletetype','address-line1 address-line2 address-line3'),
					$postcodefield = TextField::create('PostalCode','')
						->addPlaceHolder(_t('Address.POSTALCODE','Postal Code'))
						->setAttribute('data-minlength','4')
						->setAttribute('maxlength','7')
						->prependText('<span class="icon icon-envelope"></span>')
						->addExtraClass('postal-code')
						->setAttribute('x-autocompletetype','postal-code'),
					$phonefield = TextField::create('Phone','')
						->addPlaceHolder(_t('Page.CONTACTPHONELABEL','Your Phone Number'))
						->prependText('<span class="icon icon-plus"></span>')
						->setAttribute('data-mask','999-999-9999')
						->setAttribute('pattern','[0-9]{1,3}-[0-9]{1,3}-[0-9]{1,4}')
						->setAttribute('maxlength','12')
						->addExtraClass('tel phone-full')
						->setAttribute('x-autocompletetype','phone-full')
				)->addExtraClass('pull-right')
			)->addExtraClass('address-details clear-fix')

		);
		if(isset($params['addfielddescriptions']) && !empty($params['addfielddescriptions'])){
			$addressfield->setDescription(_t("Address.ADDRESSHINT","street / thoroughfare number, name, and type or P.O. Box"));
			$address2field->setDescription(_t("Address.ADDRESS2HINT","premises, building, apartment, unit, floor"));
			$cityfield->setDescription(_t("Address.CITYHINT","or suburb, county, district"));
			$statefield->setDescription(_t("Address.STATEHINT","or province, territory, island"));
		}

		$this->extend('updateFormFields', $fields);

		// set default values for testing
		if(!Director::isLive()){
			$fields->insertBefore(
				LiteralField::create(
					'AddressTestNote',
					'<div class="alert alert-block">Warning! Dummy data has been added to the form for testing convenience.</div>'
				),
				'Anchor'
			);
			$fields->setValues(array(
				'City' => 'School University',
				'State' => 'PA',
				'Address' =>  'Road 723',
				'AddressLine2' => 'h.1024, ap.50',
				'PostalCode' => '19104',
				'Phone' => '123-456-7890',
			));
		}
		//
		return $fields;
	}

	function getCountryField(){
		$countries = SiteConfig::current_site_config()->getCountriesList();
		$countryfield = ReadonlyField::create("Country",_t('Address.COUNTRY','Country'));
		if(count($countries) > 1){
			$countryfield = CountryDropdownField::create("Country",'', $countries)
				->setEmptyString(_t('Address.CHOOSECOUNTRY','(Choose Country)'))
				->setHasEmptyDefault(true)
				->addExtraClass('country-name');
		}else{
			$countryfield->setValue(array_values($countries)[0]);
		}
		return $countryfield;
	}
	
	/**
	 * Get an array of data fields that must be populated for model to be valid.
	 * Required fields can be customised via self::$required_fields
	 */
	function getRequiredFields(){
		$fields = $this->config()->required_fields;
		$this->extend('updateRequiredFields', $fields);
		return $fields;
	}
	
	/**
	 * Get full name associated with this Address
	 */
	function getName(){
		return implode('',array_filter(array(
			$this->FirstName,
			$this->Surname
		)));
	}

	/**
	 * Convert address to a single string.
	 */
	function toString($separator = ", "){
		$fields = array(
			$this->Address,
			$this->AddressLine2,
			$this->City,
			$this->State,
			$this->PostalCode,
			$this->Country
		);
		$this->extend('updateToString',$fields);
		return implode($separator,array_filter($fields));
	}

	function getTitle(){
		return $this->toString();
	}
	
	function forTemplate(){
		return $this->renderWith('Address');
	}
	
	/**
	 * Add alias setters for fields which are synonymous
	 */
	function setProvince($val){$this->State = $val;}
	function setTerritory($val){$this->State = $val;}
	function setIsland($val){$this->State = $val;}
	function setPostCode($val){$this->PostalCode = $val;}
	function setZipCode($val){$this->PostalCode = $val;}
	function setStreet($val){$this->Address = $val;}
	function setStreet2($val){$this->AddressLine2 = $val;}
	function setAddress2($val){$this->AddressLine2 = $val;}
	function setInstitution($val){$this->Company = $val;}
	function setBusiness($val){$this->Company = $val;}
	function setOrganisation($val){$this->Company = $val;}
	function setOrganization($val){$this->Company = $val;}
	
}