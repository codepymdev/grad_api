<p align="center"><img src="https://grad.fkkas.com/logo-head.png" width="150"></p>

## About Grad

Grad is an API used to communicate with the [Grad Mobile APP](https://grad.com). You can also you the api any application you want but firstly you must send a request mail to <a href="mailto:contact@grad.fkkas.com">Grad Support</a>. 
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
To use the api to really simple, you will use our endpoint `https://grad.fkkas.com/api` for all your request. 

Secondly, after verifiction from our support team you will be gaven a `school name` to uniquely identify your school. E.g: `thelordschool` which is very important because it is used in all your requests. 

### Getting all schools 
To get all schools you will send a `GET` request using this endpoint `https://grad.fkkas.com/api/schools/` this returns all the schools 

```json
[
    {
        "id": 1,
        "name": "First Kingdom kids Academy",
        "slug": "fkka",
        "logo": "https://fkkas.com/assets/img/logo.png",
        "address": "No: 16/45 First Kingdom kids street Mararaba, karu LGA, Nasarawa State",
        "status": "1",
        "principal_name": "Nduka Ebubechukwu Ifeanyi",
        "principal_avatar": "https://fkkas.com/assets/img/teachers/1.jpg",
        "proprietor_name": "Ifenyinwa Constance Nduka",
        "proprietor_avatar": "https://fkkas.com/assets/img/teachers/3.jpg",
        "created_at": "2022-03-05T14:38:59.000000Z",
        "updated_at": "2022-03-05T14:38:59.000000Z"
    },
```
### Getting active schools  
To get all active schools you will send a `GET` request using this endpoint `https://grad.fkkas.com/api/schools/active` this returns active the schools 
```json
[
    {
        "id": 1,
        "name": "First Kingdom kids Academy",
        "slug": "fkka",
        "logo": "https://fkkas.com/assets/img/logo.png",
        "address": "No: 16/45 First Kingdom kids street Mararaba, karu LGA, Nasarawa State",
        "status": "1",
        "principal_name": "Nduka Ebubechukwu Ifeanyi",
        "principal_avatar": "https://fkkas.com/assets/img/teachers/1.jpg",
        "proprietor_name": "Ifenyinwa Constance Nduka",
        "proprietor_avatar": "https://fkkas.com/assets/img/teachers/3.jpg",
        "created_at": "2022-03-05T14:38:59.000000Z",
        "updated_at": "2022-03-05T14:38:59.000000Z"
    },
```
### Getting inactive schools  
To get all inactive schools you will send a `GET` request using this endpoint `https://grad.fkkas.com/api/schools/inactive` this returns inactive the schools 

### Getting data about a particular school 
To fetch data about a school, you have to pass the ``school slug`` as the endpoint. Example `https://grad.fkkas.com/api/get/thelordsschool` 

## Auth 
### <center>Login </center>
To login as user, you will send a `POST` request passing 
- School `school name`
- Email Address (Admin, Staffs, Parents) or Student Id (Student)
- Password 

Using this endpoint `https://grad.fkkas.com/api/auth/login` 

### <center>Forget Password </center> 
To recover your password you will send a `POST` request passing the following parameters 
- Email Address 
- School `school name` 

Using this endpoint `https://grad.fkkas.com/api/auth/forgot-password` 

## People 
Getting people i.e Admin, Students, Parents and Staffs you have to send a `GET` request to the following endpoint `https://grad.fkkas.com/api/people/get/school/campus/role/type/per_page/page` 
where the following will be explained further. 
- School `school name` 
- Campus: some school come with different campus for you to get the details of the particular campus you want you pass the campus id  
- Role: You pass the role type of the current user i.e `admin, student, teaching, no-teaching, parent` 
- Type: The type of users you are fetching either students, parents or staffs 
- Per page: Number of record to show in a page, this is good for pagination 
- Page: What page you want to fetch 


## Conclusion 
Grad is an open api for those having interest in building a school management software. All you will do is use the API to make all your request you want and don't worry about others, we will do the job for you. 
You can also download the Grad mobile app <a href="#">here</a>. 

## Contact 
Having issues with the API don't worry we have you covered. You can reach us in any of the following 
- Twitter <a href="https://twitter.com/realArafatBen">@realArafatBen</a> 
- Gmail <a href="mailto:benpaul320@gmail.com">Arafat Benson</a> 
- Instagram <a href="https://www.instagram.com/realarafatben/">realarafatben</a>
