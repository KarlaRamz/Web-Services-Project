namespace ProyectoFinalAuthServiciosWeb.Models
{
    public class UserConstants
    {
        public static List<UserModel> Users = new()
        {
            new UserModel() { Username = "naeem", Password = "naeem_admin", Role = "Admin" }
        };
    }
}
