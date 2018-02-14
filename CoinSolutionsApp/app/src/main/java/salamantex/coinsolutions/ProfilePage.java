package salamantex.coinsolutions;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.graphics.Color;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.support.v4.content.LocalBroadcastManager;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.EditText;

import com.ontbee.legacyforks.cn.pedant.SweetAlert.SweetAlertDialog;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.Objects;

import salamantex.coinsolutions.Network.TalkToServer;

import static salamantex.coinsolutions.MainActivity.devEmail;
import static salamantex.coinsolutions.MainActivity.server;

/**
 * Created by EEUser on 13/02/2018.
 */

public class ProfilePage extends AppCompatActivity {

    Context activeContext;
    SweetAlertDialog pDialog;
    SweetAlertDialog activeDialog;
    String response;

    CountDownTimer countdown;

    IntentFilter filter;
    BroadcastReceiver receiver;
    boolean receiverReg = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.profile_page);
        activeContext = this;
        setReceiver();
    }

    @Override
    protected void onStart() {
        super.onStart();
        if (!receiverReg) {
            setReceiver();
        }
    }

    @Override
    protected void onResume() {
        /*This is the state in which the app interacts with the user*/
        super.onResume();
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
        if (receiverReg) {
            LocalBroadcastManager.getInstance(this).unregisterReceiver(receiver);
        }
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

    public void setReceiver() {
        filter = new IntentFilter();
        filter.addAction("serverResponseCurrency");
        receiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context activeContext, Intent intent) {
                if (intent.getAction().equals("serverResponseCurrency")) {
                    response = intent.getStringExtra("result");
                }
            }
        };
        if (!receiverReg) {
            LocalBroadcastManager.getInstance(this).registerReceiver(receiver, filter);
            receiverReg = true;
        }
    }

    public void onAddBitcoinCurrencyClick(View v) {
        String data = prepareData("btc");
        String url = server+"addCurrency.php?pass=masterpass";

        Intent msgIntent = new Intent( activeContext, TalkToServer.class);
        //Here we define the parameters (url, data)
        //basically the target php script and the data that's going to be send to it
        msgIntent.putExtra("url", url);
        msgIntent.putExtra("data",data);
        startService(msgIntent);

        pDialog = new SweetAlertDialog(this, SweetAlertDialog.PROGRESS_TYPE);
        pDialog.getProgressHelper().setBarColor(Color.parseColor("#A5DC86"));
        pDialog.setTitleText("Updating your bitcoin account");
        pDialog.setCancelable(false);
        pDialog.show();
        activeDialog = pDialog;
        countdown = new CountDownTimer(3000, 1000) {
            @Override
            public void onTick(long l) {

            }

            @Override
            public void onFinish() {
                pDialog.dismissWithAnimation();
                if (response!=null) {
                    if (!Objects.equals(response,"Your currency account has been updated successfully!")) {
                        pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.WARNING_TYPE)
                                .setTitleText("Error")
                                .setContentText("Something went wrong!")
                                .setConfirmText("Got it")
                                .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                                    @Override
                                    public void onClick(SweetAlertDialog sDialog) {
                                        pDialog.dismissWithAnimation();
                                    }
                                });
                        pDialog.show();
                    }
                    else if (Objects.equals(response,"Your currency account has been updated successfully!")) {
                        pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.SUCCESS_TYPE);
                        pDialog.setTitleText("Success");
                        pDialog.setContentText("Your bitcoin account has been updated!");
                        if (activeDialog!=null) {
                            if (activeDialog.isShowing()) {
                                activeDialog.dismissWithAnimation();
                            }
                        }
                        pDialog.show();
                    }
                }
            }
        }.start();
    }

    public void onAddEthereumCurrencyClick(View v) {
        String data = prepareData("eth");
        String url = server+"addCurrency.php?pass=masterpass";

        Intent msgIntent = new Intent( activeContext, TalkToServer.class);
        //Here we define the parameters (url, data)
        //basically the target php script and the data that's going to be send to it
        msgIntent.putExtra("url", url);
        msgIntent.putExtra("data",data);
        startService(msgIntent);

        pDialog = new SweetAlertDialog(this, SweetAlertDialog.PROGRESS_TYPE);
        pDialog.getProgressHelper().setBarColor(Color.parseColor("#A5DC86"));
        pDialog.setTitleText("Updating your ethereum account");
        pDialog.setCancelable(false);
        pDialog.show();
        activeDialog = pDialog;
        countdown = new CountDownTimer(3000, 1000) {
            @Override
            public void onTick(long l) {

            }

            @Override
            public void onFinish() {
                pDialog.dismissWithAnimation();
                if (response!=null) {
                    if (!Objects.equals(response,"Your currency account has been updated successfully!")) {
                        pDialog = new SweetAlertDialog(activeContext, SweetAlertDialog.WARNING_TYPE)
                                .setTitleText("Error")
                                .setContentText("Something went wrong!")
                                .setConfirmText("Got it")
                                .setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
                                    @Override
                                    public void onClick(SweetAlertDialog sDialog) {
                                        pDialog.dismissWithAnimation();
                                    }
                                });
                        pDialog.show();
                    }
                    else if (Objects.equals(response,"Your currency account has been updated successfully!")) {
                        pDialog.setTitleText("Success");
                        pDialog.setContentText("Your ethereum account has been updated!");
                        if (activeDialog!=null) {
                            if (activeDialog.isShowing()) {
                                activeDialog.dismissWithAnimation();
                            }
                        }
                        pDialog.show();
                    }
                }
            }
        }.start();
    }

    public void onCreateTransactionClick(View v) {
        Intent intent = new Intent(activeContext, CreateTransaction.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
        //Here maybe send data to that activity with the command below
        //intent.putExtra(EXTRA_MESSAGE, message);
        startActivity(intent);
    }

    public void showTransactionHistory(View v) {
        /*
        String data = prepareData("eth");
        String url = server+"retrieveAccounthistory.php?pass=masterpass";

        Intent msgIntent = new Intent( activeContext, TalkToServer.class);
        //Here we define the parameters (url, data)
        //basically the target php script and the data that's going to be send to it
        msgIntent.putExtra("url", url);
        msgIntent.putExtra("data",data);
        startService(msgIntent);

        pDialog = new SweetAlertDialog(this, SweetAlertDialog.PROGRESS_TYPE);
        pDialog.getProgressHelper().setBarColor(Color.parseColor("#A5DC86"));
        pDialog.setTitleText("Retrieving your account history...");
        pDialog.setCancelable(false);
        pDialog.show();
        activeDialog = pDialog;
        countdown = new CountDownTimer(3000, 1000) {
            @Override
            public void onTick(long l) {

            }

            @Override
            public void onFinish() {
                pDialog.dismissWithAnimation();
                if (response!=null) {
                    Intent intent = new Intent(activeContext, TransactionHistory.class);
                    intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
                    intent.putExtra("transactionHistory",response);
                    //Here maybe send data to that activity with the command below
                    //intent.putExtra(EXTRA_MESSAGE, message);
                    startActivity(intent);
                }
            }
        }.start();
        */
    }

    public String prepareData(String type) {
        JSONArray ar = new JSONArray();
        JSONObject obj = new JSONObject();
        try {
            if (type.equals("btc")) {
                obj.put("type","add_btc_currency");
            }
            else if (type.equals("eth")) {
                obj.put("type","add_eth_currency");
            }
            obj.put("email",devEmail);
            obj.put("account_id_btc","1rA7AB93qziWzHfTFXn5n3GYJ1mhkG8tn"); //Here it should be ideally generating it in a proper way not hardcoded of course
            obj.put("account_id_eth","");
            ar.put(obj);
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return ar.toString();
    }

}