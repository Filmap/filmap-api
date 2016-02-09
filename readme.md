# filmap_api 
[![Build Status](https://travis-ci.org/Filmap/filmap_api.svg?branch=master)](https://travis-ci.org/Filmap/filmap_api)
[![StyleCI](https://styleci.io/repos/47415696/shield)](https://styleci.io/repos/47415696)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3283d49a-03bb-40ff-a83c-a6f3fd1a6f04/mini.png)](https://insight.sensiolabs.com/projects/3283d49a-03bb-40ff-a83c-a6f3fd1a6f04)
![Safadao](https://img.shields.io/badge/safadao-aprova-orange.svg)



**Table of Contents**

- [Authenticate: [ POST ] /authenticate](#authenticate)
- [Geo calls](#geocalls)
	- [get near films: [GET] /near/{radius},{lat},{lng}](#near)
- [User-related calls](#userrelated)
	- [get all users: [ GET ] /user](#getallusers)
	- [create new user: [ POST ] /user](#createuser)
	- [get user: [ GET ] /user/{ id }](#getuser)
	- [update user: [ PUT ] /user/{ id }](#updateuser)
- [Film-related calls](#filmrelated)
	- [get all films for the authenticated user: [ GET ] /films](#getallfilms)
	- [save film: [ POST ] /films](#savefilm)
	- [Get specific film: [ GET ] /films/{id}](#getfilm)
	- [Delete specific film: [ DELETE ] /films/{id}](#deletefilm)
	- [Mark film as watched: [ POST ] /films/{omdb}/watch](#watchfilm)
- [Responses](#responses)
	- [Errors](#errors)
	- [Success](#success)


## <a name="authenticate"></a> Authenticate: [ POST ] /authenticate
Propósito: get auth token


Send a POST request with: 

    'email' = ,
    'password' = ,

Response format:

	{
		"token": xxxxxx,
	}

Salve a token para demais requisições.

Ao realizar **User-related** calls e **Film-related** calls adicione a seguinte Header:

	Authorization: Bearer [token]

## <a name="geocalls"></a> Geo calls

### <a name="near"></a> get near films: [GET] /near/{radius},{lat},{lng}

Onde:

* **radius**: radius in KM
* **lat** `<integer>`: latitude
* **lng** `<integer>`: longitude


O retorno será uma lista de filmes em um raio de `radius` KM:

	[
		{
			"omdb":,
			"lat":,
			"lng":,
			"distance":
		},
		{
			"omdb":,
			"lat":,
			"lng":,
			"distance":
		},
		...
	]

Exemplo:

	POST: /near/50,37.386339,-122.085823

Retorno:

	[
		{
			"omdb":"6",
			"lat":37.386339,
			"lng":-122.085823,
			"distance":0
		},
		{
			"omdb":"3",
			"lat":37.38714,
			"lng":-122.083235,
			"distance":0.245
		},
		{
			"omdb":"7",
			"lat":37.393885,
			"lng":-122.078916,
			"distance":1.037
		},
		{
			"omdb":"4",
			"lat":37.394011,
			"lng":-122.095528,
			"distance":1.209
		},
		{
			"omdb":"1",
			"lat":37.402653,
			"lng":-122.079354,
			"distance":1.901
		},
		{
			"omdb":"5",
			"lat":37.401724,
			"lng":-122.114646,
			"distance":3.067
		}
	]

## <a name="userrelated"></a> User-related calls

### <a name="getallusers"></a> get all users: [ GET ] /user
Propósito: get all users

Response format:

    [
    	{
    		"id":,
    		"name":,
    		"email":,
    		"created_at":,
    		"updated_at":
    	},
    	{
    		"id":,
    		"name":,
    		"email":,
    		"created_at":,
    		"updated_at":
    	},
    	...
    	,
    	{"response":true}
    ]

Onde:

**id**: id do usuário
**name**: nome do usuário
**email**: e-mail do usuário

### <a name="createuser"></a> create new user: [ POST ] /user
Send a **post** request with:

    'name' = ,
    'email' = ,
    'password' = ,
    'password_confirmation' =,


Onde:

**name**: nome do usuário
**email**: e-mail do usuário
**password**: senha
**password_confirmation**: mesma senha para confirmação

### <a name="getuser"></a> get user: [ GET ] /user/{ id }

Response format:

    {
	    "id":,
	    "name":,
	    "email":,
	    "created_at":,
	    "updated_at":,
	    
	    "response":true
    }

Onde:

**id**: id do usuário
**name**: nome do usuário
**email**: e-mail do usuário

### <a name="updateuser"></a>update user: [ PUT ] /user/{ id }

To update a user send a **put** request with (optional):

    'name' = ,
    'email' = ,
    'password' = ,
    'password_confirmation' =,


Onde:

**name**: nome do usuário
**email**: e-mail do usuário
**password**: senha
**password_confirmation**: mesma senha para confirmação

## <a name="filmrelated"></a>Film-related calls

### <a name="getallfilms"></a> get all films for the authenticated user: [ GET ] /films

Response format:

    [
    	{
    		"id":,
    		"omdb":,
    		"user_id":,
    		"watched":,
    		"created_at":,
    		"updated_at":
    	},
    	{
    		"id":,
    		"omdb":,
    		"user_id":,
    		"watched":,
    		"created_at":,
    		"updated_at":
    	},
    	...
    	,
    	{"response":true}
    ]

### <a name="savefilm"></a>save film: [ POST ] /films

Send a POST request with:

	"omdb":,
	"watched":,
	"lat":,
	"lng":

Note: **lat** and **lng** are optional inputs.

### <a name="getfilm"></a>Get specific film: [ GET ] /films/{omdb}

Response format:

    {
	    "id":,
	    "omdb":,
	    "watched":,
	    
	    "response":true
    }


### <a name="deletefilm"></a>Delete specific film: [ DELETE ] /films/{omdb}

### <a name="watchfilm"></a>Mark film as watched: [ POST ] /films/{omdb}/watch


## <a name="responses"></a>Responses

### <a name="errors"></a>Errors

Caso ocorra erro, o formato será:

    {
	    "response": false,
	    "error": <error description>,
    }

### <a name="success"></a>Success

* `POST requests` will always return `HTTP 200` to indicate that the operation was successful.

* `GET requests` will include a `"response": true` within the `JSON response`.




