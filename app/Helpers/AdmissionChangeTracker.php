<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdmissionChangeTracker
{
    /**
     * Track changes in admission form and log them to activity_log
     * 
     * @param string $familyId
     * @param array $oldData Array of old data (Consent, Guardian, Kin, Admission, Students)
     * @param array $newData Array of new data from request
     * @param int $branchId
     * @param string $branchName
     * @return void
     */
    public static function trackAdmissionChanges($familyId, $oldData, $newData, $branchId, $branchName)
    {
        $changes = [];
        $userId = auth()->id() ?? 0;

        // Track Consent changes - only if both old and new data exist
        if (!empty($oldData['consent']) && !empty($newData['consent'])) {
            $consentChanges = self::compareData($oldData['consent'], $newData['consent'], 'Consent');
            if (!empty($consentChanges)) {
                $changes['Consent'] = $consentChanges;
            }
        }

        // Track Guardian changes - only if both old and new data exist
        if (!empty($oldData['guardian']) && !empty($newData['guardian'])) {
            $guardianChanges = self::compareData($oldData['guardian'], $newData['guardian'], 'Guardian');
            if (!empty($guardianChanges)) {
                $changes['Guardian'] = $guardianChanges;
            }
        }

        // Track Kin changes - only if both old and new data exist
        if (!empty($oldData['kin']) && !empty($newData['kin'])) {
            $kinChanges = self::compareData($oldData['kin'], $newData['kin'], 'Kin');
            if (!empty($kinChanges)) {
                $changes['Kin'] = $kinChanges;
            }
        }

        // Track Admission changes - only if both old and new data exist
        if (!empty($oldData['admission']) && !empty($newData['admission'])) {
            $admissionChanges = self::compareData($oldData['admission'], $newData['admission'], 'Admission');
            if (!empty($admissionChanges)) {
                $changes['Admission'] = $admissionChanges;
            }
        }

        // Track Student changes
        if (isset($oldData['students']) && isset($newData['students'])) {
            $studentChanges = self::compareStudents($oldData['students'], $newData['students']);
            if (!empty($studentChanges)) {
                $changes['Students'] = $studentChanges;
            }
        }

        // Log changes to activity_log if there are any
        if (!empty($changes)) {
            self::logToActivityLog($familyId, $changes, $userId, $branchId, $branchName);
        }
    }

    /**
     * Compare two data arrays and return changes - ONLY TRACK SPECIFIC FIELDS
     * 
     * @param array $oldData
     * @param array $newData
     * @param string $sectionName
     * @return array
     */
    private static function compareData($oldData, $newData, $sectionName)
    {
        $changes = [];
        
        // Define which fields to track for each section
        $trackableFields = [
            'Consent' => [
                'consent_1_first_name' => 'Consent First Name',
                'consent_1_last_name' => 'Consent Last Name',
                'consent_1signature' => 'Consent Signature',
                'consent_1_date' => 'Consent Date',
                'how_did_you_hear' => 'How Did You Hear',
            ],
            'Guardian' => [
                'guardianname' => 'Guardian Name',
                'guardianaddress' => 'Guardian Address',
                'address_line_2' => 'Address Line 2',
                'city' => 'City',
                'countyStateRegion' => 'County/State/Region',
                'zIPCode' => 'ZIP Code',
                'country' => 'Country',
                'guardiantel' => 'Guardian Email',
                'guardianmob' => 'Guardian Mobile',
            ],
            'Kin' => [
                'kinname' => 'Kin Name',
                'kinaddress' => 'Kin Address',
                'emergency_conatct1_Address_line2' => 'Emergency Contact Address Line 2',
                'emergency_conatct1_city' => 'Emergency Contact City',
                'emergency_conatct1_country_state_region' => 'Emergency Contact County/State/Region',
                'emergency_conatct1_zipCode' => 'Emergency Contact ZIP Code',
                'emergency_conatct1_country' => 'Emergency Contact Country',
                'kintel' => 'Kin Email',
                'kinmob' => 'Kin Mobile',
            ],
            'Admission' => [
                'formfilingdate' => 'Form Filing Date',
                'joiningdate' => 'Joining Date',
                'medicalcondition' => 'Medical Condition',
                'feedetail' => 'Fee Detail',
                'familystatus' => 'Family Status',
                'payment_method' => 'Payment Method',
                'add_comment' => 'Additional Comment',
                'child_name1' => 'Child Name 1',
                'school_name1' => 'School Name 1',
                'child_name2' => 'Child Name 2',
                'school_name2' => 'School Name 2',
                'child_name3' => 'Child Name 3',
                'school_name3' => 'School Name 3',
                'child_name4' => 'Child Name 4',
                'school_name4' => 'School Name 4',
                'child_name5' => 'Child Name 5',
                'school_name5' => 'School Name 5',
            ],
        ];

        // Get fields to track for this section (for labels)
        $fieldsToTrack = $trackableFields[$sectionName] ?? [];
        
        // Track ALL fields from newData (not just predefined ones)
        foreach ($newData as $key => $newValue) {
            // Skip branch fields
            if (in_array($key, ['branch_id', 'branch_name'])) {
                continue;
            }
            
            $oldValue = $oldData[$key] ?? null;
            
            // Normalize values for comparison
            $oldValue = self::normalizeValue($oldValue);
            $newValue = self::normalizeValue($newValue);

            // Only add to changes if values are actually different (strict comparison)
            if ($oldValue !== $newValue) {
                // Use predefined label if available, otherwise format the key
                $fieldLabel = $fieldsToTrack[$key] ?? ucfirst(str_replace('_', ' ', $key));
                
                $changes[] = [
                    'field' => $fieldLabel,
                    'old_value' => $oldValue === null ? '(empty)' : $oldValue,
                    'new_value' => $newValue === null ? '(empty)' : $newValue
                ];
            }
        }

        return $changes;
    }

    /**
     * Compare students data
     * 
     * @param array $oldStudents
     * @param array $newStudents
     * @return array
     */
    private static function compareStudents($oldStudents, $newStudents)
    {
        $changes = [];
        
        $fieldLabels = [
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'dob' => 'Date of Birth',
            'gender' => 'Gender',
            'yearInSchool' => 'Year in School',
            'tuitionHours' => 'Tuition Hours',
            'has_medical_conditions' => 'Has Medical Conditions',
            'medical_conditions_explanation' => 'Medical Conditions Explanation',
            'has_allergies' => 'Has Allergies',
            'allergies_explanation' => 'Allergies Explanation',
            'has_additional_needs' => 'Has Additional Needs',
            'additional_needs_explanation' => 'Additional Needs Explanation',
            'medicalConsent' => 'Medical Consent',
            'photoConsent' => 'Photo Consent',
            'leaveAlone' => 'Leave Alone',
            'student_status' => 'Student Status',
        ];

        // Field mapping from database to request format
        $dbToRequestMapping = [
            'studentname' => 'firstName',
            'studentsur' => 'lastName',
            'studentdob' => 'dob',
            'studentgender' => 'gender',
            'studentyearinschool' => 'yearInSchool',
            'studenthours' => 'sessions',
            'medical_condition' => 'has_medical_conditions',
            'medicalConditions_explanation' => 'medical_conditions_explanation',
            'allergies' => 'has_allergies',
            'allergies_explanation' => 'allergies_explanation',
            'additionalNeeds' => 'has_additional_needs',
            'additional_needs_explanation' => 'additional_needs_explanation',
            'medicalConsent' => 'medicalConsent',
            'photoConsent' => 'photoConsent',
            'leaveAlone' => 'leaveAlone',
            'student_status' => 'student_status',
            // Subjects fields - Note: DB has 'tier' (singular) but form has 'tiers' (plural)
            'subject_names' => 'subject_names',
            'tier' => 'tiers', // Database column is 'tier', form field is 'tiers'
            'target_grades' => 'target_grades',
            'current_grades' => 'current_grades',
            'qualifications' => 'qualifications',
            'sessions' => 'sessions', // Also map sessions directly if exists
        ];

        // Create a map of old students by studentid
        $oldStudentsMap = [];
        foreach ($oldStudents as $oldStudent) {
            $studentId = $oldStudent['studentid'] ?? null;
            if ($studentId) {
                $oldStudentsMap[$studentId] = $oldStudent;
            }
        }

        foreach ($newStudents as $index => $newStudent) {
            $studentId = $newStudent['studentid'] ?? null;
            $studentName = trim(($newStudent['firstName'] ?? '') . ' ' . ($newStudent['lastName'] ?? ''));
            
            if ($studentId && isset($oldStudentsMap[$studentId])) {
                // Existing student - compare changes
                $oldStudent = $oldStudentsMap[$studentId];
                $studentChanges = [];
                
                // Map old student data to request format for comparison
                $mappedOldStudent = [];
                foreach ($dbToRequestMapping as $dbField => $requestField) {
                    if (isset($oldStudent[$dbField])) {
                        $mappedOldStudent[$requestField] = $oldStudent[$dbField];
                    }
                }
                
                // Debug: Also directly check for 'tier' in oldStudent if 'tiers' not found
                if (!isset($mappedOldStudent['tiers']) && isset($oldStudent['tier'])) {
                    $mappedOldStudent['tiers'] = $oldStudent['tier'];
                }
                
                // IMPORTANT: Also check if medical condition fields are already in oldStudent array
                // (they should be merged from medical_condition table in the controller)
                if (isset($oldStudent['medical_conditions_explanation'])) {
                    $mappedOldStudent['medical_conditions_explanation'] = $oldStudent['medical_conditions_explanation'];
                }
                if (isset($oldStudent['allergies_explanation'])) {
                    $mappedOldStudent['allergies_explanation'] = $oldStudent['allergies_explanation'];
                }
                if (isset($oldStudent['has_medical_conditions'])) {
                    $mappedOldStudent['has_medical_conditions'] = $oldStudent['has_medical_conditions'];
                }
                if (isset($oldStudent['has_allergies'])) {
                    $mappedOldStudent['has_allergies'] = $oldStudent['has_allergies'];
                }
                
                // Field labels for better display
                $fieldLabels = [
                    'firstName' => 'First Name',
                    'lastName' => 'Last Name',
                    'dob' => 'Date of Birth',
                    'gender' => 'Gender',
                    'yearInSchool' => 'Year in School',
                    'sessions' => 'Lessons/Sessions',
                    'has_medical_conditions' => 'Has Medical Conditions',
                    'medical_conditions_explanation' => 'Medical Conditions Explanation',
                    'has_allergies' => 'Has Allergies',
                    'allergies_explanation' => 'Allergies Explanation',
                    'has_additional_needs' => 'Has Additional Needs',
                    'additional_needs_explanation' => 'Additional Needs Explanation',
                    'medicalConsent' => 'Medical Consent',
                    'photoConsent' => 'Photo Consent',
                    'leaveAlone' => 'Leave Alone',
                    'student_status' => 'Student Status',
                    'subject_names' => 'Subjects',
                    'tiers' => 'Tiers',
                    'target_grades' => 'Target Grades',
                    'current_grades' => 'Current Grades',
                    'qualifications' => 'Qualifications',
                ];
                
                // Fields to skip (internal fields)
                $skipFields = ['studentid', 'oldName', 'gpPrefix', 'gpFirstName', 'gpLastName', 'GPPhone', 'gpAddress', 'gpAddressLineTwo', 'gp_city', 'gp_countyStateRegion', 'gpzipCode', 'gpcountry'];
                
                // Track ALL fields from newStudent (except skipped ones)
                foreach ($newStudent as $key => $newValue) {
                    // Skip internal fields
                    if (in_array($key, $skipFields)) {
                        continue;
                    }
                    
                    // Get old value
                    $oldValue = $mappedOldStudent[$key] ?? null;
                    
                    // Special handling for studenthours (sessions)
                    if ($key === 'sessions' && isset($oldStudent['studenthours'])) {
                        $oldValue = $oldStudent['studenthours'];
                    }
                    
                    // Special handling for 'tiers' - database has 'tier' column
                    if ($key === 'tiers' && !isset($mappedOldStudent[$key]) && isset($oldStudent['tier'])) {
                        $oldValue = $oldStudent['tier'];
                    }
                    
                    // Special handling for JSON/array fields (subjects data)
                    if (in_array($key, ['sessions', 'subject_names', 'tiers', 'target_grades', 'current_grades', 'qualifications', 'photoConsent'])) {
                        // Decode JSON strings to arrays
                        $oldValueRaw = $oldValue;
                        $newValueRaw = $newValue;
                        
                        $oldValue = is_string($oldValue) ? json_decode($oldValue, true) : $oldValue;
                        $newValue = is_array($newValue) ? $newValue : (is_string($newValue) ? json_decode($newValue, true) : $newValue);
                        
                        // Compare arrays properly
                        $oldArray = is_array($oldValue) ? $oldValue : [];
                        $newArray = is_array($newValue) ? $newValue : [];
                        
                        // Remove empty/null/N/A values for comparison
                        $oldArrayFiltered = array_filter($oldArray, function($item) {
                            return $item !== null && $item !== '' && $item !== 'N/A' && trim($item) !== '';
                        });
                        $newArrayFiltered = array_filter($newArray, function($item) {
                            return $item !== null && $item !== '' && $item !== 'N/A' && trim($item) !== '';
                        });
                        
                        // Sort arrays for consistent comparison
                        sort($oldArrayFiltered);
                        sort($newArrayFiltered);
                        
                        // Compare arrays - if both are empty after filtering, no change
                        if (empty($oldArrayFiltered) && empty($newArrayFiltered)) {
                            continue; // Skip - no actual change
                        }
                        
                        // Compare arrays
                        $oldArrayJson = !empty($oldArrayFiltered) ? json_encode($oldArrayFiltered) : null;
                        $newArrayJson = !empty($newArrayFiltered) ? json_encode($newArrayFiltered) : null;
                        
                        // Only track if actually different
                        if ($oldArrayJson !== $newArrayJson) {
                            $fieldLabel = $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                            
                            // Format display values - preserve original arrays for display
                            // Get original old value from database
                            $oldArrayForDisplay = [];
                            
                            // Special handling for 'tiers' - database has 'tier' column
                            $dbKey = $key;
                            if ($key === 'tiers') {
                                $dbKey = 'tier'; // Database column is 'tier'
                            }
                            
                            if (isset($oldStudent[$dbKey])) {
                                $oldArrayForDisplay = is_string($oldStudent[$dbKey]) ? json_decode($oldStudent[$dbKey], true) : (is_array($oldStudent[$dbKey]) ? $oldStudent[$dbKey] : []);
                            } elseif (isset($mappedOldStudent[$key])) {
                                $oldArrayForDisplay = is_string($mappedOldStudent[$key]) ? json_decode($mappedOldStudent[$key], true) : (is_array($mappedOldStudent[$key]) ? $mappedOldStudent[$key] : []);
                            } else {
                                $oldArrayForDisplay = [];
                            }
                            
                            // Ensure $oldArrayForDisplay is an array (json_decode might return int/string)
                            if (!is_array($oldArrayForDisplay)) {
                                $oldArrayForDisplay = [];
                            }
                            
                            // Get new value from request
                            $newArrayForDisplay = is_array($newValueRaw) ? $newValueRaw : (is_string($newValueRaw) ? json_decode($newValueRaw, true) : []);
                            
                            // Ensure $newArrayForDisplay is an array (json_decode might return int/string)
                            if (!is_array($newArrayForDisplay)) {
                                $newArrayForDisplay = [];
                            }
                            
                            // Filter out empty/null values for display but keep structure
                            $oldArrayForDisplay = array_filter($oldArrayForDisplay, function($item) {
                                return $item !== null && $item !== '' && $item !== 'N/A';
                            });
                            $newArrayForDisplay = array_filter($newArrayForDisplay, function($item) {
                                return $item !== null && $item !== '' && $item !== 'N/A';
                            });
                            
                            $displayOld = !empty($oldArrayForDisplay) ? implode(', ', $oldArrayForDisplay) : '(empty)';
                            $displayNew = !empty($newArrayForDisplay) ? implode(', ', $newArrayForDisplay) : '(empty)';
                            
                            $studentChanges[] = [
                                'field' => $fieldLabel,
                                'old_value' => $displayOld,
                                'new_value' => $displayNew
                            ];
                        }
                    } else {
                        // Normalize other values for comparison
                        $oldValueNormalized = self::normalizeValue($oldValue);
                        $newValueNormalized = self::normalizeValue($newValue);
                        
                        // Special handling: if both are null/empty/"no", treat as same (no change)
                        if ($oldValueNormalized === null && $newValueNormalized === null) {
                            // Both are empty - no change, skip
                            continue;
                        }
                        
                        // Special handling for medical/allergy fields - only track if there's actual content change
                        if (in_array($key, ['has_medical_conditions', 'has_allergies'])) {
                            // Normalize both values for comparison
                            $oldValueStr = is_string($oldValue) ? strtolower(trim($oldValue)) : '';
                            $newValueStr = is_string($newValue) ? strtolower(trim($newValue)) : '';
                            
                            // Treat null, empty, and "no" as equivalent
                            $oldIsEmpty = ($oldValueNormalized === null || $oldValueStr === '' || $oldValueStr === 'no');
                            $newIsEmpty = ($newValueStr === '' || $newValueStr === 'no' || $newValueNormalized === null);
                            
                            // If both are empty (null/"no"/empty), don't track
                            if ($oldIsEmpty && $newIsEmpty) {
                                continue;
                            }
                            
                            // If both are "yes", don't track (no change)
                            if ($oldValueStr === 'yes' && $newValueStr === 'yes') {
                                continue;
                            }
                            
                            // If old is empty and new is "yes", this IS a change - track it
                            // If old is "yes" and new is empty, this IS a change - track it
                            // Only skip if both are same
                        }
                        
                        // For explanation fields, only track if there's actual content change
                        if (in_array($key, ['medical_conditions_explanation', 'allergies_explanation'])) {
                            // Get trimmed values for comparison
                            $oldValueTrimmed = is_string($oldValue) ? trim($oldValue) : ($oldValue ?? '');
                            $newValueTrimmed = is_string($newValue) ? trim($newValue) : ($newValue ?? '');
                            
                            // Normalize empty strings to null
                            if ($oldValueTrimmed === '') {
                                $oldValueTrimmed = null;
                            }
                            if ($newValueTrimmed === '') {
                                $newValueTrimmed = null;
                            }
                            
                            // If both are empty/null, don't track
                            if ($oldValueTrimmed === null && $newValueTrimmed === null) {
                                continue;
                            }
                            
                            // If both have same content after trimming, don't track
                            if ($oldValueTrimmed === $newValueTrimmed && $oldValueTrimmed !== null) {
                                continue;
                            }
                            
                            // Special case: Check parent field logic
                            if ($key === 'medical_conditions_explanation') {
                                $parentOldValue = $mappedOldStudent['has_medical_conditions'] ?? null;
                                $parentNewValue = $newStudent['has_medical_conditions'] ?? null;
                                
                                // Normalize parent values
                                $parentOldValueStr = is_string($parentOldValue) ? strtolower(trim($parentOldValue)) : '';
                                $parentNewValueStr = is_string($parentNewValue) ? strtolower(trim($parentNewValue)) : '';
                                
                                // If old parent was not 'yes' and new parent is also not 'yes', 
                                // and old explanation was null, don't track (form logic: explanation only if parent is 'yes')
                                if (($parentOldValueStr !== 'yes' && $parentNewValueStr !== 'yes') && 
                                    $oldValueTrimmed === null && $newValueTrimmed !== null) {
                                    // This is invalid data - explanation shouldn't exist if parent is not 'yes'
                                    continue;
                                }
                                
                                // If old parent was 'yes' and new parent is 'yes', and both explanations are same, don't track
                                if ($parentOldValueStr === 'yes' && $parentNewValueStr === 'yes' && 
                                    $oldValueTrimmed === $newValueTrimmed && $oldValueTrimmed !== null) {
                                    continue;
                                }
                                
                                // CRITICAL: If old parent was 'yes' but old explanation is null, 
                                // and new parent is 'yes' with explanation, this might be a data capture issue
                                // Check if maybe the old explanation was actually there but we didn't capture it
                                // In this case, if new value matches what might have been there, don't track
                                // But we can't know for sure, so we'll track it as a change
                                // However, if old parent was null/empty and new parent is 'yes', this IS a change
                            }
                            
                            if ($key === 'allergies_explanation') {
                                $parentOldValue = $mappedOldStudent['has_allergies'] ?? null;
                                $parentNewValue = $newStudent['has_allergies'] ?? null;
                                
                                // Normalize parent values
                                $parentOldValueStr = is_string($parentOldValue) ? strtolower(trim($parentOldValue)) : '';
                                $parentNewValueStr = is_string($parentNewValue) ? strtolower(trim($parentNewValue)) : '';
                                
                                // If old parent was not 'yes' and new parent is also not 'yes', 
                                // and old explanation was null, don't track (form logic: explanation only if parent is 'yes')
                                if (($parentOldValueStr !== 'yes' && $parentNewValueStr !== 'yes') && 
                                    $oldValueTrimmed === null && $newValueTrimmed !== null) {
                                    // This is invalid data - explanation shouldn't exist if parent is not 'yes'
                                    continue;
                                }
                                
                                // If old parent was 'yes' and new parent is 'yes', and both explanations are same, don't track
                                if ($parentOldValueStr === 'yes' && $parentNewValueStr === 'yes' && 
                                    $oldValueTrimmed === $newValueTrimmed && $oldValueTrimmed !== null) {
                                    continue;
                                }
                            }
                        }
                        
                        // Only add if values are actually different
                        if ($oldValueNormalized !== $newValueNormalized) {
                            $fieldLabel = $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                            
                            // Format display values - use original values for display
                            $displayOld = self::formatDisplayValue($oldValue);
                            $displayNew = self::formatDisplayValue($newValue);
                            
                            $studentChanges[] = [
                                'field' => $fieldLabel,
                                'old_value' => $displayOld,
                                'new_value' => $displayNew
                            ];
                        }
                    }
                }
                
                if (!empty($studentChanges)) {
                    $changes[] = [
                        'student_name' => $studentName ?: 'Student #' . ($index + 1),
                        'student_id' => $studentId,
                        'changes' => $studentChanges
                    ];
                }
            } elseif (empty($studentId)) {
                // New student
                $studentName = trim(($newStudent['firstName'] ?? '') . ' ' . ($newStudent['lastName'] ?? ''));
                $changes[] = [
                    'student_name' => $studentName ?: 'New Student #' . ($index + 1),
                    'student_id' => null,
                    'action' => 'added',
                    'changes' => [['field' => 'Status', 'old_value' => '(empty)', 'new_value' => 'New student added']]
                ];
            }
        }

        return $changes;
    }

    /**
     * Normalize value for comparison - Better handling
     * 
     * @param mixed $value
     * @return string|null
     */
    private static function normalizeValue($value)
    {
        // Handle null
        if ($value === null) {
            return null;
        }
        
        // Handle empty string - convert to null for comparison
        if ($value === '') {
            return null;
        }
        
        // Handle boolean
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        
        // Handle arrays - sort and encode for consistent comparison
        if (is_array($value)) {
            // Remove empty values from array
            $value = array_filter($value, function($item) {
                return $item !== null && $item !== '' && $item !== 'N/A';
            });
            
            if (empty($value)) {
                return null;
            }
            
            // Sort array keys for consistent comparison
            if (array_values($value) === $value) {
                // Numeric array - sort values
                sort($value);
            } else {
                // Associative array - sort by keys
                ksort($value);
            }
            return json_encode($value);
        }
        
        // Trim string values
        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }
            
            // Treat "no", "No", "NO" as null for comparison (same as empty)
            $lowerValue = strtolower($value);
            if ($lowerValue === 'no' || $lowerValue === 'n/a' || $lowerValue === 'na') {
                return null;
            }
            
            return $value;
        }
        
        return (string) $value;
    }

    /**
     * Format value for display
     * 
     * @param mixed $value
     * @return string
     */
    private static function formatDisplayValue($value)
    {
        if ($value === null || $value === '') {
            return '(empty)';
        }
        
        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '' || strtolower($trimmed) === 'no' || strtolower($trimmed) === 'n/a') {
                return '(empty)';
            }
            return $trimmed;
        }
        
        if (is_array($value)) {
            $filtered = array_filter($value, function($item) {
                return $item !== null && $item !== '' && $item !== 'N/A';
            });
            if (empty($filtered)) {
                return '(empty)';
            }
            return implode(', ', $filtered);
        }
        
        return (string) $value;
    }

    /**
     * Log changes to activity_log table
     * 
     * @param string $familyId
     * @param array $changes
     * @param int $userId
     * @param int $branchId
     * @param string $branchName
     * @return void
     */
    private static function logToActivityLog($familyId, $changes, $userId, $branchId, $branchName)
    {
        // Build description
        $descriptionParts = [];
        foreach ($changes as $section => $sectionChanges) {
            if ($section === 'Students') {
                foreach ($sectionChanges as $studentChange) {
                    $studentName = $studentChange['student_name'] ?? 'Student';
                    if (isset($studentChange['action']) && $studentChange['action'] === 'added') {
                        $descriptionParts[] = "Student '{$studentName}' was added";
                    } else {
                        $changeCount = count($studentChange['changes'] ?? []);
                        if ($changeCount > 0) {
                            $descriptionParts[] = "Student '{$studentName}': " . $changeCount . " field(s) changed";
                        }
                    }
                }
            } else {
                $changeCount = count($sectionChanges);
                if ($changeCount > 0) {
                    $descriptionParts[] = "{$section}: {$changeCount} field(s) changed";
                }
            }
        }

        $description = "Admission form updated (Family ID: {$familyId}). " . implode('; ', $descriptionParts);

        // Build insert data
        $insertData = [
            'log_name' => 'Admission',
            'description' => $description,
            'subject_type' => 'App\\Models\\Admission',
            'subject_id' => null,
            'event' => 'updated',
            'causer_type' => 'App\\Models\\User',
            'causer_id' => $userId,
            'properties' => json_encode([
                'family_id' => $familyId,
                'changes' => $changes,
                'branch_id' => $branchId,
                'branch_name' => $branchName,
            ]),
            'branch_id' => $branchId,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Try to add family_id column if it exists
        // We'll try-catch the insert to handle missing column gracefully
        try {
            $insertData['family_id'] = $familyId;
            DB::table('activity_log')->insert($insertData);
        } catch (\Exception $e) {
            // If family_id column doesn't exist, remove it and try again
            unset($insertData['family_id']);
            DB::table('activity_log')->insert($insertData);
        }
    }
}

