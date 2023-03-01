package com.composrfoundation.codequalitychecker;

import java.awt.*;

import javax.swing.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class AboutDialog extends JDialog {

    JPanel panel1 = new JPanel();
    JButton closeBtn = new JButton();
    JLabel jLabel1 = new JLabel();
    JLabel jLabel2 = new JLabel();

    public AboutDialog(Frame owner, String title, boolean modal) {
        super(owner, title, modal);
        try {
            setDefaultCloseOperation(DISPOSE_ON_CLOSE);
            jbInit();
            pack();
        } catch (Exception exception) {
            exception.printStackTrace(System.out);
        }
    }

    public AboutDialog() {
        this(new Frame(), "About", false);
    }

    private void jbInit() throws Exception {
        panel1.setLayout(null);
        closeBtn.setActionCommand("closeBtn");
        closeBtn.addActionListener(new AboutDialog_closeBtn_actionAdapter(this));
        jLabel1.setText("<html>This is the Web Code Quality Checker, for checking web documents against web standards.<br><br>See the readme.txt file for full documentation.</html>");
        jLabel1.setBounds(new Rectangle(20, 31, 349, 70));
        jLabel2.setToolTipText("");
        jLabel2.setText("<html>Copyright (c) ocProducts Ltd, 2004-2023.</html>");
        jLabel2.setBounds(new Rectangle(20, 196, 348, 40));
        this.setModal(true);
        this.setResizable(false);
        this.setTitle("About");
        this.getContentPane().add(panel1, null);
        panel1.add(closeBtn);
        panel1.add(jLabel1);
        panel1.add(jLabel2);
        closeBtn.setBounds(new Rectangle(153, 255, 71, 23));
        closeBtn.setText("Close");
        panel1.setPreferredSize(new Dimension(400, 300));
    }

    public void closeBtn_actionPerformed(ActionEvent e) {
        this.setVisible(false);
    }
}

class AboutDialog_closeBtn_actionAdapter implements ActionListener {

    private final AboutDialog adaptee;

    AboutDialog_closeBtn_actionAdapter(AboutDialog adaptee) {
        this.adaptee = adaptee;
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        adaptee.closeBtn_actionPerformed(e);
    }
}
