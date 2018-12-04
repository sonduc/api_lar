<?php

use Illuminate\Database\Seeder;

class GuidebookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('guidebook_category')->insert([
    		[
    			'name' => 'Parks & Nature',
    			'icon' => 'fab fa-pagelines',      
    			'lang' => 'en'        
    		],
    		
    		[
    			'name' => 'Drinks & Nightlife',
    			'icon' => 'fas fa-coffee',       
    			'lang' => 'en'        
    		],   

    		[
    			'name' => 'Food Scene',
    			'icon' => 'fas fa-utensils',        
    			'lang' => 'en'        
    		], 

    		[
    			'name' => 'Shopping',
    			'icon' => 'fas fa-shopping-bag',        
    			'lang' => 'en'        
    		],  

    		[
    			'name' => 'Entertainment & Activities',
    			'icon' => 'fas fa-gamepad',        
    			'lang' => 'en'        
    		],  

    		[
    			'name' => 'Getting Around',
    			'icon' => 'fas fa-bus-alt',        
    			'lang' => 'en'        
    		],

    		[
    			'name' => 'Công viên',
    			'icon' => 'fab fa-pagelines',      
    			'lang' => 'vi'        
    		],
    		
    		[
    			'name' => 'Coffee / Bar / Pub',
    			'icon' => 'fas fa-coffee',       
    			'lang' => 'vi'        
    		],   

    		[
    			'name' => 'Ẩm thực',
    			'icon' => 'fas fa-utensils',        
    			'lang' => 'vi'        
    		], 

    		[
    			'name' => 'Khu mua sắm',
    			'icon' => 'fas fa-shopping-bag',        
    			'lang' => 'vi'        
    		],  

    		[
    			'name' => 'Giải trí',
    			'icon' => 'fas fa-gamepad',        
    			'lang' => 'vi'        
    		],  

    		[
    			'name' => 'Phương tiện công cộng',
    			'icon' => 'fas fa-bus-alt',        
    			'lang' => 'vi'        
    		],
    	]);
    }
}
