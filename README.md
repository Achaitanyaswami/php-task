# Laravel 6.x task
### Summary	
This is a laravel 6.x project to develop the REST API for customers and providers. 
Using following technologies:
- Mysql 5.7+
- Php 7.2
- Laravel 6.0

### Introduction
In this project, there is a two types of users i.e. `Customer` and `Provider`. Users with `Provider` role can create a `Provider Profile`. Additonally, provider will select the `services` he offers along with the price of each `service`. `Provider` will select these `services` from existing list, it will be the choice of `provider` to either choose the same price as in list or he can choose to enter his own price . `Customers` will search for nearby `providers` for example all the providers located with in **2KM** distance from user's location. `Customer` can send a request to a `provider`. Upon receiving the request, `provider` can either `accept` or `reject` the request. All rejected requests or requests older than 24 hours should be cleared from database every day.

### Additional Information
- Created a `services` table with `title, price`. Usind the seeders in laravel to initialize the database with initial catgories
- Created a `users` table with `name, email, mobile_number, latitude, longitude`.
- Assigned `customer` or `provider` role to a user
- Created a `provider_profiles` table with `name, description, latitude, longitude`
- Every `provider` can choose muliple services i.e each provider can belong to many categories
- For each selected `service`, provider can choose the already defined price in `service` or can enter his own price
	- For example, suppose we have following `services` in database
		1. Category 1 with price 10,
		2. Category 2 with price 20
		
		Now, `Provider 1` select the services 'Service 1' and 'Service 2' i.e. provider belongs to both of these services
		
		- For `Service 1` provider can decide to keep same price as defined i.e 10.
		- But for `Service 2` store an decide to change the price to 30
- Created REST APIs for following operations
	- Login and registration for customer and provider
	- Customer APIs:
		- **Note**: These APIs are accesbile only from `customer` credentials. These APIs is not be accessible if I login using `provider` credentials:
		- List all nearby `providers` i.e. providers located with in 2KM from customer's location
		- Send a request to `provider`
	- Provider APIs:
		- Update `provider profile` along with `services` that this provider belongs to
		- `accept` or `reject` customer request
