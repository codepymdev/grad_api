<p align="center"><img src="https://www.codepym.com/assets/img/photos/grad.png" width="150"></p>

## About Grad

Grad is an API used to communicate with the [Grad FrontEnd](https://grad.codepym.com). You can also you the api any application you want but firstly you must send a request mail to <a href="mailto:contact@codepym.com">Grad Support</a>. 
### Features
#
- Student, Parent, Staff and Admin Login
- Download Results.
- View Classes, Subjects, All Users.
- Live messages
- Notifications.
- Events.
- Announcements.
- Much more.. 

## How to use the API 
To use the api to really simple, you will use our endpoint `https://grad.codepym.com/api` for all your request. 

Secondly, after verifiction from our support team you will be gaven a `school name` to uniquely identify your school. E.g: `thelordschool` which is very important because it is used in all your requests. 

### Getting all schools 
To get all schools you will send a `GET` request using this endpoint `https://grad.codepym.com/api/schools/` this returns all the schools 

```json
[
    {
        "id": 1,
        "name": "First Kingdom kids Academy",
        "slug": "fkka",
        "logo": "https://codepym.com/assets/img/logo.png",
        "address": "No: 16/45 First Kingdom kids street Mararaba, karu LGA, Nasarawa State",
        "status": "1",
        "principal_name": "Nduka Ebubechukwu Ifeanyi",
        "principal_avatar": "https://codepym.com/assets/img/teachers/1.jpg",
        "proprietor_name": "Ifenyinwa Constance Nduka",
        "proprietor_avatar": "https://codepym.com/assets/img/teachers/3.jpg",
        "created_at": "2022-03-05T14:38:59.000000Z",
        "updated_at": "2022-03-05T14:38:59.000000Z"
    },
```
### Getting active schools  
To get all active schools you will send a `GET` request using this endpoint `https://grad.codepym.com/api/schools/active` this returns active the schools 
```json
[
    {
        "id": 1,
        "name": "First Kingdom kids Academy",
        "slug": "fkka",
        "logo": "https://codepym.com/assets/img/logo.png",
        "address": "No: 16/45 First Kingdom kids street Mararaba, karu LGA, Nasarawa State",
        "status": "1",
        "principal_name": "Nduka Ebubechukwu Ifeanyi",
        "principal_avatar": "https://codepym.com/assets/img/teachers/1.jpg",
        "proprietor_name": "Ifenyinwa Constance Nduka",
        "proprietor_avatar": "https://codepym.com/assets/img/teachers/3.jpg",
        "created_at": "2022-03-05T14:38:59.000000Z",
        "updated_at": "2022-03-05T14:38:59.000000Z"
    },
```
### Getting inactive schools  
To get all inactive schools you will send a `GET` request using this endpoint `https://grad.codepym.com/api/schools/inactive` this returns inactive the schools 

### Getting data about a particular school 
To fetch data about a school, you have to pass the ``school slug`` as the endpoint. Example `https://grad.codepym.com/api/get/thelordsschool` 

## Auth 
### <center>Login </center>
To login as user, you will send a `POST` request passing 
- School `school name`
- Email Address (Admin, Staffs, Parents) or Student Id (Student)
- Password 

Using this endpoint `https://grad.codepym.com/api/auth/login` 

### <center>Forget Password </center> 
To recover your password you will send a `POST` request passing the following parameters 
- Email Address 
- School `school name` 

Using this endpoint `https://grad.codepym.com/api/auth/forgot-password` 

## People 
Getting people i.e Admin, Students, Parents and Staffs you have to send a `GET` request to the following endpoint `https://grad.codepym.com/api/people/get/school/campus/role/type/per_page/page` 
where the following will be explained further. 
- School `school name` 
- Campus: some school come with different campus for you to get the details of the particular campus you want you pass the campus id  
- Role: You pass the role type of the current user i.e `admin, student, teaching, no-teaching, parent` 
- Type: The type of users you are fetching either students, parents or staffs 
- Per page: Number of record to show in a page, this is good for pagination 
- Page: What page you want to fetch 


### How to contribute

Here are some ways of contributing to making Grad better:

- Send a pull request to any of our [open source repositories](https://github.com/codepymdev) on GitHub. Check the contribution guide on the repo you want to contribute to for more details about how to contribute. We're looking forward to your contribution!

