package salamantex.coinsolutions;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;

import com.ontbee.legacyforks.cn.pedant.SweetAlert.SweetAlertDialog;

public class MainActivity extends AppCompatActivity {

    public static String server = "http://giannispetridis.xyz/CoinSolutions/Scripts/";
    public static String devEmail = "";
    public static Class activeClass;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        activeClass = this.getClass();
    }

    @Override
    protected void onStart() {
        super.onStart();
    }

    @Override
    protected void onResume() {
        /*This is the state in which the app interacts with the user*/
        super.onResume();
        //setView(R.layout.first);
        //refreshPicture();
    }

    @Override
    protected void onPause() {
        super.onPause();
    }

    @Override
    protected void onStop() {
        super.onStop();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
    }

    @Override
    public void onBackPressed() {
        new SweetAlertDialog(this, SweetAlertDialog.WARNING_TYPE)
                .setTitleText("Really Exit?")
                .setContentText("Are you sure you want to exit?")
                .setConfirmText("No")
                .setCancelText("Yes")
                .showCancelButton(true)
                .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                    @Override
                    public void onClick(SweetAlertDialog sDialog) {
                        sDialog.dismissWithAnimation();

                    }
                })
                .setCancelClickListener(new SweetAlertDialog.OnSweetClickListener() {
                    @Override
                    public void onClick(SweetAlertDialog sDialog) {
                        sDialog.dismissWithAnimation();
                        finishAffinity();
                    }
                })
                .show();
    }

    public void onLoginClick(View v) {
        Intent intent = new Intent(this, LoginPage.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
        //Here maybe send data to that activity with the command below
        //intent.putExtra(EXTRA_MESSAGE, message);
        startActivity(intent);
    }

    public void onRegisterClick(View v) {
        Intent intent = new Intent(this, RegisterPage.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
        //Here maybe send data to that activity with the command below
        //intent.putExtra(EXTRA_MESSAGE, message);
        startActivity(intent);
    }

}
