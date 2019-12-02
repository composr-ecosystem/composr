package com.composrfoundation.codequalitychecker;

import java.awt.*;

import javax.swing.*;

public class ProcessingDialog extends JDialog {

    JPanel panel1 = new JPanel();
    BorderLayout borderLayout1 = new BorderLayout();
    JLabel jLabel1 = new JLabel();

    public ProcessingDialog(Frame owner, String title, boolean modal) {
        super(owner, title, modal);
        try {
            setDefaultCloseOperation(DISPOSE_ON_CLOSE);
            jbInit();
            pack();
        } catch (Exception exception) {
            exception.printStackTrace(System.out);
        }
    }

    public ProcessingDialog() {
        this(new Frame(), "ProcessingDialog", false);
    }

    private void jbInit() throws Exception {
        panel1.setLayout(borderLayout1);
        jLabel1.setFont(new java.awt.Font("Dialog", Font.PLAIN, 20));
        jLabel1.setText("Running PHP backend. Please wait....");
        this.setModal(false);
        this.setResizable(false);
        this.setTitle("Please wait...");
        getContentPane().add(panel1);
        panel1.add(jLabel1, java.awt.BorderLayout.WEST);
        panel1.setPreferredSize(new Dimension(347, 48));
        this.setSize(new Dimension(347, 48));
    }
}
