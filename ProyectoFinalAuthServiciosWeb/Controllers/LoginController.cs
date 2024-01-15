using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using ProyectoFinalAuthServiciosWeb.Models;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.IdentityModel.Tokens;
using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;
using FireSharp.Config;
using FireSharp.Response;
using FireSharp.Interfaces;
using FireSharp;

namespace ProyectoFinalAuthServiciosWeb.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class LoginController : Controller
    {
        private readonly IConfiguration _config;
        public LoginController(IConfiguration config)
        {
            _config = config;
        }
        [AllowAnonymous]
        //[HttpPost]
        //[ValidateAntiForgeryToken]
        [Microsoft.AspNetCore.Mvc.HttpPost]
        public async Task<ActionResult> LoginAsync([FromBody] UserLogin userLogin)
        {
            IFirebaseConfig ifc = new FirebaseConfig()
            {
                AuthSecret = "Ifu7r1gkkQs2TPyZtYmpTYT3kLVzo0iKIJf7vv8x",
                BasePath = "https://practica6-ffc02-default-rtdb.firebaseio.com/"
            };

            IFirebaseClient client;
            client = new FirebaseClient(ifc);
            FirebaseResponse res = await client.GetAsync(@"py/users/ok2");
            Console.WriteLine(res);
            if (res.Body.ToString() != "null")
            {
                var securityKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(_config["Jwt:Key"]));
                var credentials = new SigningCredentials(securityKey, SecurityAlgorithms.HmacSha256);
                var claims = new[]
                {
                    new Claim(ClaimTypes.NameIdentifier,userLogin.Username),
                    new Claim(ClaimTypes.NameIdentifier,"User Authentication"),
                    new Claim(ClaimTypes.NameIdentifier, "200"),
                };

                //var user = Authenticate(userLogin);
                ////var user = UserConstants.Users
                //if (user != null)
                //{
                //UserModel user = new UserModel();
                //user.Username = userLogin.Username;
                //user.Password = userLogin.Password;
                //user.Role = userLogin.;
                //var token = GenerateToken(user);
                //var token ="";
                //return Ok(token);
                //}

                //return NotFound("user not found");
                var token = new JwtSecurityToken(_config["Jwt:Issuer"],
                    _config["Jwt:Audience"],
                    claims,
                    expires: DateTime.Now.AddMinutes(80),
                    signingCredentials: credentials);

                var data = new JwtSecurityTokenHandler().WriteToken(token);
                return Ok(data);
            }
            else 
            {
                return BadRequest("Error");
            }
        }

        // To generate token
        //private string GenerateToken(UserModel user)
        //[AllowAnonymous]
        //[Microsoft.AspNetCore.Mvc.HttpPost]
        //public ActionResult GenerateToken([FromBody] UserLogin user) 
        //{
        //    var securityKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(_config["Jwt:Key"]));
        //    var credentials = new SigningCredentials(securityKey, SecurityAlgorithms.HmacSha256);
        //    var claims = new[]
        //    {
        //        new Claim(ClaimTypes.NameIdentifier,user.Username),
        //    };
        //    var token = new JwtSecurityToken(_config["Jwt:Issuer"],
        //        _config["Jwt:Audience"],
        //        claims,
        //        expires: DateTime.Now.AddMinutes(15),
        //        signingCredentials: credentials);

        //    var data = new JwtSecurityTokenHandler().WriteToken(token);
        //    return Ok(data);
        //}
        //To authenticate user

    }
}
