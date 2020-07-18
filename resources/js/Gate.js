export default class Gate
{
    constructor(user)
    {
        this.user = user;
    }

    isAdmin()
    {
        return this.user.type === 'admin';
    }

    isUser()
    {
        return this.user.type === 'user';
    }

    isTeacher()
    {
        return this.user.type === 'teacher';
    }

    isStudent()
    {
        return this.user.type === 'student';
    }
}