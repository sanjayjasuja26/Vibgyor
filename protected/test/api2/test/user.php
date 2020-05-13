     <?php
    return [
        'user' => [
            
            'login' => [
                'LoginForm[username]' => 'harman',
                'LoginForm[password]' => 'admin',
                'LoginForm[device_token]' => '12131313',
                'LoginForm[device_type]' => '1',
                'LoginForm[device_name]' => '1'
            ],
            
            'add-home-value' => [
                'HomeValue[username]' => 'harman',
                'HomeValue[email]' => 'admin@toxsl.in',
                'HomeValue[contact_no]' => '12131313',
                'HomeValue[address]' => 'Test String',
                'HomeValue[latitude]' => '1646',
                'HomeValue[longitude]' => '54646',
                'HomeValue[bedroom]' => '1',
                'HomeValue[basement]' => '1',
                'HomeValue[bathroom]' => '1',
                'HomeValue[home_type_id]' => '1',
                'HomeValue[professional_id]' => '1'
            ],
            
            'check' => [
                'DeviceDetail[device_token]' => 'harman',
                'DeviceDetail[device_type]' => 'admin',
                'DeviceDetail[device_name]' => '12131313'
            
            ],
            
            'initiate' => [
                'InitiateForm[name]' => 'harman',
                'InitiateForm[email]' => ''
            
            ],
            
            'contact-us' => [
                'ContactForm[name]' => 'harman',
                'ContactForm[email]' => 'admin@gmail.com',
                'ContactForm[subject]' => 'Test String',
                'ContactForm[body]' => 'Test String',
                'ContactForm[phone_number]' => 'Test String'
            
            ],
            'change-password' => [
                'User[password]' => ''
            ],
            
            'complaint-resolve' => [
                'ComplaintResolve[message]' => ''
            ],
            
            'signup' => [
                'User[full_name]' => 'Test String',
                'User[email]' => 'Trand' . rand(0, 499) . 'est@' . rand(0, 499) . 'String.com',
                'User[password]' => 'Test String',
                'User[address]' => '',
                'User[latitude]' => 'Test String',
                'User[longitude]' => 'Test String',
                'User[contact_no]' => 'Test String',
                'User[role_id]' => '',
                'Professional[profession_type_id]' => ''
            ],
            'change-password' => [
                'User[oldPassword]' => 'Test String',
                'User[newPassword]' => 'Test String',
                'User[confirm_password]' => 'Test String'
            
            ],
            'update-profile' => [
                'User[contact_no]' => '',
                'User[profile_file]' => '',
                'User[about_me]' => '',
                'AreaCode[zip_code]' => '',
                'Professional[fax]' => '',
                'Professional[office_no]' => '',
                'Professional[credit_score_id]' => '',
                'Professional[budget_id]' => '',
                'Professional[down_payment_id]' => '',
                'Professional[time_period_id]' => '',
                'Professional[representaion_id]' => '',
                'Professional[purpose_id]' => '',
                'Professional[property_type_id]' => '',
                'Professional[website_link]' => '',
                'Professional[calculator_url]' => '',
                'Professional[company_info]' => '',
                'Professional[association_us]' => '',
                'Professional[licence]' => '',
                'Professional[specialities]' => '',
                'Professional[video_link]' => '',
                'Professional[facebook_link]' => '',
                'Professional[twiter_link]' => '',
                'Professional[googleplus_link]' => '',
                'Professional[linkedin_link]' => '',
                'Professional[youtube_link]' => ''
            
            ],
            'update-social' => [
                'Professional[facebook_link]' => 'Test String',
                'Professional[twiter_link]' => '',
                'Professional[googleplus_link]' => '',
                'Professional[linkedin_link]' => '',
                'Professional[youtube_link]' => ''
            
            ],
            'add-featured-image' => [
                'FeaturedImage[image]' => '',
                'FeaturedImage[purpose]' => '',
                'FeaturedImage[price]' => '',
                'FeaturedImage[bedroom]' => '',
                'FeaturedImage[bathroom]' => '',
                'FeaturedImage[area]' => '',
                'FeaturedImage[location]' => '',
                'FeaturedImage[description]' => ''
            ],
            
            'search-professional' => [
                'User[type]' => 'Test String',
                'User[name]' => 'Test String'
            
            ],
            
            'search-agent' => [
                'User[type]' => 'Test String',
                'User[pro-type]' => 'Test String',
                'User[lat]' => 'Test String',
                'User[long]' => 'Test String',
                'User[city]' => 'Test String',
                'SearchRecord[property_type_id]' => 'Test String',
                'SearchRecord[budget_id]' => 'Test String',
                'SearchRecord[time_period_id]' => 'Test String',
                'SearchRecord[want_to_buy]' => 'Test String',
                'SearchRecord[want_to_sell]' => 'Test String',
                'HomeLoans[property_type_id]' => '',
                'HomeLoans[use_of_property]' => '',
                'HomeLoans[plan]' => '',
                'HomeLoans[found_home]' => '',
                'HomeLoans[already_working]' => '',
                'HomeLoans[budget_id]' => '',
                'HomeLoans[down_payment_id]' => '',
                'HomeLoans[credit_score_id]' => '',
                'HomeLoans[in_military]' => '',
                'HomeLoans[bankruptcy_foreclosure]' => '',
                'HomeLoans[bankruptcy_time]' => '',
                'HomeLoans[foreclosure_time]' => '',
                'HomeInspection[property_type_id]' => '',
                'HomeInspection[time_period_id]' => '',
                'HomeInspection[budget_id]' => '',
                'TitleAgent[property_type_id]' => '',
                'TitleAgent[time_period_id]' => '',
                'TitleAgent[representation_id]' => '',
                'TitleAgent[have_agent]' => ''
            
            ],
            'feedback' => [
                'Feedback[name]' => 'Test String',
                'Feedback[email]' => 'Test String',
                'Feedback[feedback]' => 'Test String',
                'Feedback[suggestion]' => 'Test String',
                'Feedback[agent_id]' => 'Test String',
                'Feedback[agent_name]' => 'Test String',
                'Feedback[agent_type]' => 'Test String',
                'Feedback[agent_email]' => 'Test String',
                'Feedback[agent_contact]' => 'Test String',
                'Feedback[type_id]' => 'Test String'
            ],
            'instagram' => [
                "User[email]" => "",
                "User[userId]" => "",
                "User[provider]" => "",
                "User[full_name]" => "",
                // "User[image_url]"=>'',
                "User[device_token]" => '',
                "User[device_type]" => ''
            ],
            'send-message' => [
                "Chatmessage[to_user_id]" => "",
                "Chatmessage[to_user_name]" => "",
                "Chatmessage[message]" => ""
            
            ],
            'add-blogs' => [
                "BlogPost[title]" => "",
                "BlogPost[content]" => "",
                "BlogPost[image_file]" => ""
            
            ],
            'fix-appointment' => [
                "ContactForm[name]" => "",
                "ContactForm[email]" => "",
                "ContactForm[subject]" => "",
                "ContactForm[phone_number]" => "",
                "ContactForm[appointment_date]" => "",
                "ContactForm[appointment_time]" => "",
                "ContactForm[body]" => ""
            
            ]
        ]
    
    ];
    ?>
