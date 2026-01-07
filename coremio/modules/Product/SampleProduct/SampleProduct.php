<?php
    class SampleProduct extends ProductModule
    {
        function __construct(){
            $this->_name = __CLASS__;
            parent::__construct();
        }


        public function configuration()
        {
            $action             = isset($_GET["action"]) ? $_GET["action"] : false;
            $action             = Filter::letters_numbers($action);

            $vars = [
                'm_name'        => $this->_name,
                'area_link'     => $this->area_link,
                'lang'          => $this->lang,
                'config'        => $this->config,
            ];
            return $this->get_page("configuration".($action ? "-".$action : ''),$vars);
        }

        /*
         * Configuration with Form Elements -> Controller
        */
        public function controller_save()
        {
            $example1       = Filter::init("POST/example1","hclear");
            $example2       = Filter::init("POST/example2","password");
            $example3       = (int) Filter::init("POST/example3","numbers");

            $set_config     = $this->config;

            if($set_config["settings"]["example1"] != $example1) $set_config["settings"]["example1"] = $example1;
            if($set_config["settings"]["example2"] != $example2) $set_config["settings"]["example2"] = $example2;
            if($set_config["settings"]["example3"] != $example3) $set_config["settings"]["example3"] = $example3;

            if(Validation::isEmpty($example1))
            {
                echo Utility::jencode([
                    'status' => "error",
                    'message' => $this->lang["error1"],
                ]);
                return false;
            }

            $this->save_config($set_config);


            echo Utility::jencode([
                'status' => "successful",
                'message' => $this->lang["success1"],
            ]);

            return true;
        }

        public function config_options($data=[])
        {
            return [
                'example1'          => [
                    'name'              => "Text Box",
                    'description'       => "Text Box Description",
                    'type'              => "text",
                    'width'             => "50",
                    'value'             => isset($data["example1"]) ? $data["example1"] : "sample",
                    'placeholder'       => "sample placeholder",
                ],
                'example2'          => [
                    'name'              => "Password Box",
                    'description'       => "Password Box Description",
                    'type'              => "password",
                    'width'             => "50",
                    'value'             => isset($data["example2"]) ? $data["example2"] : "sample",
                    'placeholder'       => "sample placeholder",
                ],
                'example3'          => [
                    'name'              => "Approval Button",
                    'description'       => "Approval Button Description",
                    'type'              => "approval",
                    'checked'           => isset($data["example3"]) && $data["example3"] ? true : false,
                ],
                'example4'          => [
                    'name'              => "Dropdown Menu 1",
                    'description'       => "Dropdown Menu 1 Description",
                    'type'              => "dropdown",
                    'options'           => "Option 1,Option 2,Option 3,Option 4",
                    'value'             => isset($data["example4"]) ? $data["example4"] : "Option 2",
                ],
                'example5'          => [
                    'name'              => "Dropdown Menu 2",
                    'description'       => "Dropdown Menu 2 Description",
                    'type'              => "dropdown",
                    'options'           => [
                        'opt1'     => "Option 1",
                        'opt2'     => "Option 2",
                        'opt3'     => "Option 3",
                        'opt4'     => "Option 4",
                    ],
                    'value'             => isset($data["example5"]) ? $data["example5"] : "opt2",
                ],
                'example6'          => [
                    'name'              => "Circular(Radio) Button 1",
                    'description'       => "Circular(Radio) Button 1",
                    'width'             => 40,
                    'description_pos'   => 'L',
                    'is_tooltip'        => true,
                    'type'              => "radio",
                    'options'           => "Option 1,Option 2,Option 3,Option 4",
                    'value'             => isset($data["example6"]) ? $data["example6"] : "Option 2",
                ],
                'example7'          => [
                    'name'              => "Circular(Radio) Button 2",
                    'description'       => "Circular(Radio) Button 2 Description",
                    'description_pos'   => 'L',
                    'is_tooltip'        => true,
                    'type'              => "radio",
                    'options'           => [
                        'opt1'     => "Option 1",
                        'opt2'     => "Option 2",
                        'opt3'     => "Option 3",
                        'opt4'     => "Option 4",
                    ],
                    'value'             => isset($data["example7"]) ? $data["example7"] : "opt2",
                ],
                'example8'          => [
                    'name'              => "Text Area",
                    'description'       => "Text Area Description",
                    'rows'              => "3",
                    'type'              => "textarea",
                    'value'             => isset($data["example8"]) ? $data["example8"] : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
                    'placeholder'       => "sample placeholder",
                ],
            ];
        }

        /**
         * (Not Required) When a product addon is purchased, you can use this function if you need to perform an action on the module according to the purchased addon.
         * @param array $addon Transmits the data of the linked row in the users_products_addons table in the database.
         * @deprecated  array $args Will be deprecated after 3.2.
         * @return bool|array If you return an array as a return value, it stores this array as JSON in the "module_data" column of the corresponding row in the "users_products_addons" table in the database. If you don't need to store data, you can return bool data depending on the transaction state.
         */
        public function addon_create($addon=[], $args=[]):bool|array
        {
            $entity_id  = $this->order["options"]["config"]["id"] ?? 0;

            if(!$entity_id)
            {
                $this->error = "The connected service is not yet established.";
                return false;
            }

            $values = $this->id_of_conf_opt[$addon['id']] ?? [];

            // Sample: Buy Backup
            if(($values["Backup"] ?? []))
            {
                #$value = $values["Backup"]; // Ex: "Daily" or "Weekly"
                #$response = $this->api->EnableBackup($entity_id,$value);
                $response    = ['status' => "successful"];
                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }
                return true;
            }

            // Sample: Buy Extra IP Address
            elseif(($values["ExtraIP"] ?? []))
            {

                #$value = $values["ExtraIP"]; //  Ex: "5","10","15"
                #$response = $this->api->AddExtraIPAddress($value,$entity_id);
                $response    = [
                    'id' => 123,
                    'status' => "successful",
                    'data' => [
                        '192.168.1.1',
                        '192.168.1.2',
                        '192.168.1.3',
                    ],
                ];

                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }

                // Updating the IP addresses assigned to the order
                $options = $this->order["options"] ?? [];
                $assigned_ips = explode("\n",$options["assigned_ips"] ?? '');
                foreach($response["data"] AS $ip) $assigned_ips[] = $ip;
                $options["assigned_ips"] = implode("\n",$assigned_ips);
                Orders::set($this->order["id"],['options' => Utility::jencode($options)]);

                // Return addon module_data
                return [
                    'id' => $response["id"]
                ];
            }

            return true;
        }
        /**
         * (Not Required) Use this function if you also want to take action on the module when the status of the ordered product addon is suspended.
         * @param array $addon Transmits the data of the linked row in the users_products_addons table in the database.
         * @deprecated array $args Will be deprecated after 3.2.
         * @return bool|array If you return an array as a return value, it stores this array as JSON in the "module_data" column of the corresponding row in the "users_products_addons" table in the database. If you don't need to store data, you can return bool data depending on the transaction state.
         */
        public function addon_suspend($addon=[],$args=[])
        {
            $entity_id  = $this->order["options"]["config"]["id"] ?? 0;
            if(!$entity_id) return true;

            $values         = $this->id_of_conf_opt[$addon['id']] ?? [];
            $module_data    = $addon['module_data'] ?? [];

            // Sample: Suspend Backup
            if(($values["Backup"] ?? []))
            {
                #$response = $this->api->DisableBackup($entity_id);
                $response    = ['status' => "successful"];
                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }
                return true;
            }

            // Sample: Suspend Extra IP Address
            elseif(($values["ExtraIP"] ?? []))
            {
                #$id = $module_data["id"] ?? 0;
                #$response = $this->api->DisableExtraIPAddress($id);
                $response    = ['status' => "successful"];

                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }
                return true;
            }

            return true;
        }

        /**
         * (Not Required) Use this function if you also want to take action on the module when the status of the ordered product addon is unsuspended.
         * @param array $addon Transmits the data of the linked row in the users_products_addons table in the database.
         * @deprecated array $args Will be deprecated after 3.2.
         * @return bool|array If you return an array as a return value, it stores this array as JSON in the "module_data" column of the corresponding row in the "users_products_addons" table in the database. If you don't need to store data, you can return bool data depending on the transaction state.
         */

        public function addon_unsuspend($addon=[],$args=[])
        {
            $entity_id  = $this->order["options"]["config"]["id"] ?? 0;
            if(!$entity_id) return true;

            $values         = $this->id_of_conf_opt[$addon['id']] ?? [];
            $module_data    = $addon['module_data'] ?? [];

            // Sample: Unsuspend Backup
            if(($values["Backup"] ?? []))
            {
                #$response = $this->api->EnableBackup($entity_id);
                $response    = ['status' => "successful"];
                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }
                return true;
            }

            // Sample: Unsuspend Extra IP Address
            elseif(($values["ExtraIP"] ?? []))
            {
                #$id = $module_data["id"] ?? 0;
                #$response = $this->api->EnableExtraIPAddress($id);
                $response    = [
                    'status' => "successful",
                ];

                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }
                return true;
            }

            return true;
        }

        /**
         * (Not Required) Use this function if you also want to take action on the module when the status of the ordered product addon is cancelled.
         * @param array $addon Transmits the data of the linked row in the users_products_addons table in the database.
         * @deprecated array $params Will be deprecated after 3.2.
         * @return bool|array If you return an array as a return value, it stores this array as JSON in the "module_data" column of the corresponding row in the "users_products_addons" table in the database. If you don't need to store data, you can return bool data depending on the transaction state.
         */
        public function addon_cancelled($addon=[],$params=[])
        {
            $entity_id  = $this->order["options"]["config"]["id"] ?? 0;
            if(!$entity_id) return true;

            $values         = $this->id_of_conf_opt[$addon['id']] ?? [];
            $module_data    = $addon['module_data'] ?? [];

            // Sample: Cancel Backup
            if(($values["Backup"] ?? []))
            {
                #$response = $this->api->CancelBackup($entity_id);
                $response    = ['status' => "successful"];
                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }
                return true;
            }

            // Sample: Cancel Extra IP Address
            elseif(($values["ExtraIP"] ?? []))
            {
                #$id = $module_data["id"] ?? 0;
                #$response = $this->api->CancelExtraIPAddress($id);
                $response    = [
                    'status' => "successful",
                    'ip_addresses' => [
                        '192.168.1.1',
                        '192.168.1.2',
                        '192.168.1.3',
                    ],
                ];

                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }

                $options = $this->order["options"] ?? [];
                $assigned_ips = explode("\n",$options["assigned_ips"] ?? '');
                // Clear ips
                foreach($response["ip_addresses"] AS $ip)
                    if(in_array($ip,$assigned_ips))
                        unset($assigned_ips[array_search($ip,$assigned_ips)]);
                $options["assigned_ips"] = implode("\n",$assigned_ips);
                Orders::set($this->order["id"],['options' => Utility::jencode($options)]);
                return [];
            }

            return true;
        }

        /**
         * (Not Required) Use this function if you also want to take action on the module when the details of the ordered product addon is changed.
         * @param array $addon Transmits the data of the linked row in the users_products_addons table in the database.
         * @param array $args Database "users_products_addons" table after modification of the related row
         * @return bool|array If you return an array as a return value, it stores this array as JSON in the "module_data" column of the corresponding row in the "users_products_addons" table in the database. If you don't need to store data, you can return bool data depending on the transaction state.
         */

        public function addon_change($addon=[],$args=[])
        {
            $entity_id  = $this->order["options"]["config"]["id"] ?? 0;
            if(!$entity_id) return true;

            $values         = $this->id_of_conf_opt[$addon['id']] ?? [];
            $module_data    = $addon['module_data'] ?? [];


            // Sample: Change Extra IP Address
            if(($values["ExtraIP"] ?? []))
            {
                $q      = (int) $args["option_quantity"] ?? 0;
                #$id = $module_data["id"] ?? 0;
                #$response = $this->api->ChangeIPAddressCount($id,$q);
                $response    = [
                    'status' => "successful",
                    'data' => [
                        '192.168.1.1',
                        '192.168.1.2',
                        '192.168.1.3',
                    ],
                ];

                if(!$response)
                {
                    $this->error = $this->api->error;
                    return false;
                }

                // Updating the IP addresses assigned to the order
                $options = $this->order["options"] ?? [];
                $assigned_ips = explode("\n",$options["assigned_ips"] ?? '');
                foreach($response["data"] AS $ip) if(!in_array($ip,$assigned_ips)) $assigned_ips[] = $ip;
                $options["assigned_ips"] = implode("\n",$assigned_ips);
                Orders::set($this->order["id"],['options' => Utility::jencode($options)]);

                return true;
            }

            return true;
        }

        public function create($order_options=[])
        {
            # Parameters Docs: https://docs.wisecp.com/en/kb/product-module-development-parameters

            try
            {
                // API POST Parameters
                $api_post_data = [
                    'example1' => $order_options["creation_info"]["example1"],
                    'example2' => $order_options["creation_info"]["example2"],
                    'example3' => $order_options["creation_info"]["example3"],
                ];

                // Fetch configurable options (Optional)
                if($this->val_of_conf_opt["ExtraIP"] ?? false)
                    $api_post_data["ExtraIP"] = $this->val_of_conf_opt["ExtraIP"];

                // Fetch requirement (Optional)
                if($this->val_of_requirements["Example1"] ?? false)
                    $api_post_data["example1"] = $this->val_of_requirements["Example1"];

                # API call code here...

                $api_response = [
                    'status' => "successful",
                    'id' => 1234,
                ];

                if($api_response["status"] != "successful") throw new Exception($api_response["message"]);


                return [
                    'config' => [
                        'id' => $api_response['id'],
                    ],
                ];

            }
            catch (Exception $e){
                $this->error = $e->getMessage();
                self::save_log(
                    'Product',
                    $this->_name,
                    __FUNCTION__,
                    ['order' => $this->order],
                    $e->getMessage(),
                    $e->getTraceAsString()
                );
                return false;
            }
        }

        public function renewal($order_options=[])
        {
            # Parameters Docs: https://docs.wisecp.com/en/kb/product-module-development-parameters

            $entity_id = $order_options["config"]["id"] ?? 0;

            try
            {
                if(!$entity_id) throw new Exception("Entity ID not found.");

                // API submit code here...
                #$response = $this->api->renewal($entity_id);
                $response = ['status' => "successful"];

                if($response["status"] != "successful") throw new Exception($response["message"]);

                return true;
            }
            catch (Exception $e){
                $this->error = $e->getMessage();
                self::save_log(
                    'Product',
                    $this->_name,
                    __FUNCTION__,
                    ['order' => $this->order],
                    $e->getMessage(),
                    $e->getTraceAsString()
                );
                return false;
            }
        }

        public function apply_updowngrade($product=[]){
            # Parameters Docs: https://docs.wisecp.com/en/kb/product-module-development-parameters
            $o_config               = $this->order["options"]["config"];
            $o_creation_info        = $this->order["options"]["creation_info"];
            $p_creation_info        = $product["module_data"];

            $entity_id              = $o_config["id"] ?? 0;

            try
            {
                if(!$entity_id) throw new Exception("Entity ID not found.");

                $params                 = [
                    'example1'  => $p_creation_info["example1"],
                    'example2'  => $p_creation_info["example2"],
                ];

                $result = ['status' => "successful"]; # $api->upgrade($params);

                if($result["status"] != "successful") throw new Exception($result["message"]);


                return true;
            }
            catch (Exception $e){
                $this->error = $e->getMessage();
                self::save_log(
                    'Product',
                    $this->_name,
                    __FUNCTION__,
                    ['order' => $this->order],
                    $e->getMessage(),
                    $e->getTraceAsString()
                );
                return false;
            }
        }

        public function suspend()
        {
            # Parameters Docs: https://docs.wisecp.com/en/kb/product-module-development-parameters
            $entity_id              = $this->order["options"]["config"]["id"] ?? 0;

            try
            {
                if(!$entity_id) throw new Exception("Entity ID not found.");
                #$result = $this->api->suspend($entity_id);
                $result = ['status' => "successful"];

                if($result["status"] != "successful") throw new Exception($result["message"]);

                return true;
            }
            catch (Exception $e){
                $this->error = $e->getMessage();
                self::save_log(
                    'Product',
                    $this->_name,
                    __FUNCTION__,
                    ['order' => $this->order],
                    $e->getMessage(),
                    $e->getTraceAsString()
                );
                return false;
            }
        }

        public function unsuspend()
        {
            # Parameters Docs: https://docs.wisecp.com/en/kb/product-module-development-parameters
            $entity_id              = $this->order["options"]["config"]["id"] ?? 0;

            try
            {
                if(!$entity_id) throw new Exception("Entity ID not found.");
                #$result = $this->api->unsuspend($entity_id);
                $result = ['status' => "successful"];
                if($result["status"] != "successful") throw new Exception($result["message"]);

                return true;
            }
            catch (Exception $e){
                $this->error = $e->getMessage();
                self::save_log(
                    'Product',
                    $this->_name,
                    __FUNCTION__,
                    ['order' => $this->order],
                    $e->getMessage(),
                    $e->getTraceAsString()
                );
                return false;
            }
        }

        public function delete()
        {
            # Parameters Docs: https://docs.wisecp.com/en/kb/product-module-development-parameters
            $entity_id              = $this->order["options"]["config"]["id"] ?? 0;
            try
            {
                if(!$entity_id) throw new Exception("Entity ID not found.");
                #$result = $this->api->delete($entity_id);
                $result = ['status' => "successful"];

                if($result["status"] != "successful") throw new Exception($result["message"]);

                return true;
            }
            catch (Exception $e){
                $this->error = $e->getMessage();
                self::save_log(
                    'Product',
                    $this->_name,
                    __FUNCTION__,
                    ['order' => $this->order],
                    $e->getMessage(),
                    $e->getTraceAsString()
                );
                return false;
            }
        }

        public function clientArea()
        {
            # Parameters Docs: https://docs.wisecp.com/en/kb/product-module-development-parameters
            $entity_id  = $this->order["options"]["config"]["id"] ?? 0;
            $content    = $this->clientArea_buttons_output();
            $_page      = $this->page;

            if(!$_page) $_page = 'home';

            $content .= $this->get_page('clientArea-'.$_page,['test1' => 'hello world', 'test2' => 'sample var']);
            return  $content;
        }

        public function clientArea_buttons()
        {
            $buttons    = [];

            if($this->page && $this->page != "home")
            {
                $buttons['home'] = [
                    'text' => $this->lang["turn-back"],
                    'type' => 'page-loader',
                ];
            }
            else
            {

                $buttons['custom_transaction'] = [
                    'text'  => 'Run Transaction',
                    'type'  => 'transaction',
                ];

                $buttons['another'] = [
                    'text'  => 'Another Page',
                    'type'  => 'page-loader',
                ];

                $buttons['custom_function'] = [
                    'text'  => 'Open Function',
                    'type'  => 'function',
                    'target_blank' => true,
                ];

                $buttons['another-link'] = [
                    'text'      => 'Another link',
                    'type'      => 'link',
                    'url'       => 'https://www.google.com',
                    'target_blank' => true,
                ];
            }

            return $buttons;
        }

        public function use_clientArea_another(){
            echo Utility::jencode([
                'status' => "error",
                'message' => "Example Error Message",
            ]);
        }

        public function use_clientArea_custom_transaction()
        {
            echo  Utility::jencode([
                'status' => "successful",
                'message' => 'Successful Transaction',
            ]);

            return true;
        }

        public function use_clientArea_custom_function()
        {
            if(Filter::POST("var2"))
            {
                echo  Utility::jencode([
                    'status' => "successful",
                    'message' => 'Successful message',
                ]);
            }
            else
            {
                echo "Content Here...";
            }

            return true;
        }

        public function use_clientArea_SingleSignOn()
        {
            $api_result     = 'OK|bmd5d0p384ax7t26zr9wlwo4f62cf8g6z0ld';

            if(substr($api_result,0,2) != 'OK'){
                echo "An error has occurred, unable to access.";
                return false;
            }

            $token          = substr($api_result,2);
            $url            = 'https://modulewebsite.com/api/access/'.$token;

            Utility::redirect($url);

            echo "Redirecting...";
        }

        public function use_clientArea_webMail()
        {
            $url            = 'https://modulewebsite.com/webmail';

            Utility::redirect($url);

            echo "Redirecting...";
        }


        public function adminArea_buttons()
        {
            $buttons = [];

            $buttons['custom_transaction'] = [
                'text'  => 'Run Transaction',
                'type'  => 'transaction',
            ];
            $buttons['custom_function'] = [
                'text'  => 'Open Function',
                'type'  => 'function',
                'target_blank' => true,
            ];

            $buttons['another-link'] = [
                'text'      => 'Another link',
                'type'      => 'link',
                'url'       => 'https://www.google.com',
                'target_blank' => true,
            ];

            return $buttons;
        }

        public function use_adminArea_custom_transaction()
        {
            echo  Utility::jencode([
                'status' => "successful",
                'message' => 'Successful Transaction',
            ]);

            return true;
        }

        public function use_adminArea_custom_function()
        {
            if(Filter::POST("var2"))
            {
                echo  Utility::jencode([
                    'status' => "successful",
                    'message' => 'Successful message',
                ]);
            }
            else
            {
                echo "Content Here...";
            }

            return true;
        }

        public function adminArea_service_fields(){
            $c_info                 = $this->options["creation_info"];
            $field1                 = $c_info["field1"] ?? null;
            $field2                 = $c_info["field2"] ?? null;

            return [
                'field1'                => [
                    'wrap_width'        => 100,
                    'name'              => "Field 1",
                    'description'       => "Field 1 Description",
                    'type'              => "text",
                    'value'             => $field1,
                    'placeholder'       => "sample placeholder",
                ],
                'field2'                => [
                    'wrap_width'        => 100,
                    'name'              => "Field 2",
                    'type'              => "output",
                    'value'             => '<input type="number" name="creation_info[field2]" value="'.$field2.'">',
                ],
            ];
        }

        public function save_adminArea_service_fields($data=[]){

            /* OLD DATA */
            $c_info           = $data['old']['creation_info'];
            $o_config           = $data['old']['config'];
            $o_options          = $data['old']['options'];

            /* NEW DATA */

            $n_c_info           = $data['new']['creation_info'];
            $n_config           = $data['new']['config'];
            $n_options          = $data['new']['options'];

            if($n_c_info['field1'] == '')
            {
                $this->error = 'Do not leave Field 1 empty.';
                return false;
            }

            return [
                'creation_info'     => $n_c_info,
                'config'            => $n_config,
                'options'           => $n_options,
            ];
        }

    }