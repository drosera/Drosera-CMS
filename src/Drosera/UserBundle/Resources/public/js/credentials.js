function grant(credentialName)
{
    var url=$('#drosera_user_credential_grantUrl').val();

    $.post(url,{
        user_group_id:$("#drosera_user_credential_user_group").val(),
        credential_name:credentialName          
    },function(data){
        $('#'+credentialName).css('background-color', 'green');
        $('#'+credentialName).removeAttr('onclick');
        $('#'+credentialName).unbind('click');
        $('#'+credentialName).click(function() {
            revoke(credentialName);
            return false;
        });   
    },"json");
    return false; 
}

function revoke(credentialName)
{
    var url=$('#drosera_user_credential_revokeUrl').val();

    $.post(url,{
        user_group_id:$("#drosera_user_credential_user_group").val(),
        credential_name:credentialName          
    },function(data){
        $('#'+credentialName).css('background-color', 'red');
        $('#'+credentialName).removeAttr('onclick');
        $('#'+credentialName).unbind('click');
        $('#'+credentialName).click(function() {          
            grant(credentialName);
            return false;
        });
    },"json");
    return false; 
}

$(document).ready(function() {
    var getCredentials = function(){
        var url='#';

        $.post(url,{
            user_group_id:$("#drosera_user_credential_user_group").val()          
        },function(data){
            $('#credentials-box').html(data);
        },"html");
        return false;
    }; 

    getCredentials();
    $("#drosera_user_credential_user_group").change(getCredentials);
});