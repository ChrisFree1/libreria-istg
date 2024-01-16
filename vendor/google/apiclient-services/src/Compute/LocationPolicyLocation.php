<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Compute;

class LocationPolicyLocation extends \Google\Model
{
  /**
   * @var LocationPolicyLocationConstraints
   */
  public $constraints;
  protected $constraintsType = LocationPolicyLocationConstraints::class;
  protected $constraintsDataType = '';
  /**
   * @var string
   */
  public $preference;

  /**
   * @param LocationPolicyLocationConstraints
   */
  public function setConstraints(LocationPolicyLocationConstraints $constraints)
  {
    $this->constraints = $constraints;
  }
  /**
   * @return LocationPolicyLocationConstraints
   */
  public function getConstraints()
  {
    return $this->constraints;
  }
  /**
   * @param string
   */
  public function setPreference($preference)
  {
    $this->preference = $preference;
  }
  /**
   * @return string
   */
  public function getPreference()
  {
    return $this->preference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationPolicyLocation::class, 'Google_Service_Compute_LocationPolicyLocation');