<%@ Page Language="C#" %>
<%@ Import Namespace="System.Web.Security" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<script runat="server">
public void Login_OnClick(object sender, EventArgs args)
{
   if (FormsAuthentication.Authenticate(UsernameTextbox.Text, PasswordTextbox.Text))
      {FormsAuthentication.SetAuthCookie(UsernameTextbox.Text, NotPublicCheckBox.Checked);
        Response.Redirect("public/index.html");
      }
   else
     Msg.Text = "Login failed. Please check your user name and password and try again.";
}
</script>
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>Login</title>
</head>
<body>
<form id="form1" runat="server">
  <h3>Login</h3>
  <asp:Label id="Msg" ForeColor="maroon" runat="server" /><br />
  Username: <asp:Textbox id="UsernameTextbox" runat="server" /><br />
  Password: <asp:Textbox id="PasswordTextbox" runat="server" TextMode="Password" /><br />
  <asp:Button id="LoginButton" Text="Login" OnClick="Login_OnClick" runat="server" />
  <asp:CheckBox id="NotPublicCheckBox" runat="server" /> 
  Check here if this is <span style="text-decoration:underline">not</span> a public computer.
</form>
</body>
</html>