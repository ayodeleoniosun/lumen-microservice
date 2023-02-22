# Lumen based microservice based app

### Overview of the application
If you want to ship a new software quickly and you don't have much resources in terms of finance and engineers, monolithic architecture is the way to go.

Think of Monolithic architecture as "everything together as one". It is a traditional way of building softwares from scratch as a single unit. It has a number of pros like ease of development and deployment, non-complex testing,  and is applicable in a small and medium codebase.

However, there comes a time when the codebase grows bigger and updates to a part of the codebase leads to a break in other parts because the application is tightly coupled together.

This is the problem that microservice architectural style solves.

Microservice architecture is a software architectural style that structure applications as a collection of loosely connected services, making it easier to build and scale applications.

In this repo, I built a prototype of a microservice based application architecture with Laravel Lumen framework which can be cloned and used for your project.

It consists of two services, namely: PostService and CommentService and a API gateway.

The API Gateway is responsible for user authentication and it also routes requests to the appropriate microservice which in turn returns the appropriate response.

A direct access to any of the services is restricted through the middleware `ApiToken`.

The API Gateway uses an Oauth2 token based authentication for the microservices built with Laravel Passport.

### Architecture of the application

Attached below is a pictorial architecture of the application:

### General installation guides

#### Step 1: Clone the repository

```bash
git clone https://github.com/ayodeleoniosun/lumen-microservice.git
```

#### Step 2: Switch to the repo folder

```bash
cd lumen-microservice
```

Navigate to API Gateway and each service to see their respective installation guides.

### API Documentation

The Postman API collection is available [Here](postman_collection.json). <br/>