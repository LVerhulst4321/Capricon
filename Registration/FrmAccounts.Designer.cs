﻿namespace Registration
{
    partial class FrmAccounts
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmAccounts));
            this.LstPeople = new System.Windows.Forms.ListView();
            this.colID = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colFirstName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colLastName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colEmail = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colPhone = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.SortImages = new System.Windows.Forms.ImageList(this.components);
            this.BtnSearch = new System.Windows.Forms.Button();
            this.TxtLastName = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.TxtEmail = new System.Windows.Forms.TextBox();
            this.label3 = new System.Windows.Forms.Label();
            this.TxtBadgeNumber = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.TxtID = new System.Windows.Forms.TextBox();
            this.label5 = new System.Windows.Forms.Label();
            this.BtnClear = new System.Windows.Forms.Button();
            this.SuspendLayout();
            // 
            // LstPeople
            // 
            this.LstPeople.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.LstPeople.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colID,
            this.colFirstName,
            this.colLastName,
            this.colEmail,
            this.colPhone});
            this.LstPeople.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstPeople.FullRowSelect = true;
            this.LstPeople.GridLines = true;
            this.LstPeople.Location = new System.Drawing.Point(0, 127);
            this.LstPeople.MultiSelect = false;
            this.LstPeople.Name = "LstPeople";
            this.LstPeople.Size = new System.Drawing.Size(683, 372);
            this.LstPeople.SmallImageList = this.SortImages;
            this.LstPeople.TabIndex = 0;
            this.LstPeople.UseCompatibleStateImageBehavior = false;
            this.LstPeople.View = System.Windows.Forms.View.Details;
            this.LstPeople.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.LstPeople_ColumnClick);
            this.LstPeople.DoubleClick += new System.EventHandler(this.LstPeople_DoubleClick);
            // 
            // colID
            // 
            this.colID.Text = "#";
            this.colID.Width = 44;
            // 
            // colFirstName
            // 
            this.colFirstName.Text = "First Name";
            this.colFirstName.Width = 143;
            // 
            // colLastName
            // 
            this.colLastName.Text = "Last Name";
            this.colLastName.Width = 159;
            // 
            // colEmail
            // 
            this.colEmail.Text = "Email Address";
            this.colEmail.Width = 184;
            // 
            // colPhone
            // 
            this.colPhone.Text = "Primary Phone #";
            this.colPhone.Width = 125;
            // 
            // SortImages
            // 
            this.SortImages.ImageStream = ((System.Windows.Forms.ImageListStreamer)(resources.GetObject("SortImages.ImageStream")));
            this.SortImages.TransparentColor = System.Drawing.Color.Transparent;
            this.SortImages.Images.SetKeyName(0, "Down.png");
            this.SortImages.Images.SetKeyName(1, "Up.png");
            // 
            // BtnSearch
            // 
            this.BtnSearch.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSearch.Location = new System.Drawing.Point(327, 76);
            this.BtnSearch.Name = "BtnSearch";
            this.BtnSearch.Size = new System.Drawing.Size(130, 27);
            this.BtnSearch.TabIndex = 1;
            this.BtnSearch.Text = "Search Database";
            this.BtnSearch.UseVisualStyleBackColor = true;
            this.BtnSearch.Click += new System.EventHandler(this.BtnSearch_Click);
            // 
            // TxtLastName
            // 
            this.TxtLastName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtLastName.Location = new System.Drawing.Point(138, 35);
            this.TxtLastName.Name = "TxtLastName";
            this.TxtLastName.Size = new System.Drawing.Size(159, 20);
            this.TxtLastName.TabIndex = 2;
            this.TxtLastName.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.SearchField_KeyPress);
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(45, 38);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(87, 13);
            this.label1.TabIndex = 3;
            this.label1.Text = "Last Name:";
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(13, 65);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(119, 13);
            this.label2.TabIndex = 5;
            this.label2.Text = "Email Address:";
            // 
            // TxtEmail
            // 
            this.TxtEmail.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtEmail.Location = new System.Drawing.Point(138, 61);
            this.TxtEmail.Name = "TxtEmail";
            this.TxtEmail.Size = new System.Drawing.Size(159, 20);
            this.TxtEmail.TabIndex = 4;
            this.TxtEmail.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.SearchField_KeyPress);
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(21, 90);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(111, 13);
            this.label3.TabIndex = 7;
            this.label3.Text = "Badge Number:";
            // 
            // TxtBadgeNumber
            // 
            this.TxtBadgeNumber.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtBadgeNumber.Location = new System.Drawing.Point(138, 87);
            this.TxtBadgeNumber.Name = "TxtBadgeNumber";
            this.TxtBadgeNumber.Size = new System.Drawing.Size(159, 20);
            this.TxtBadgeNumber.TabIndex = 6;
            this.TxtBadgeNumber.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.SearchField_KeyPress);
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(85, 12);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(47, 13);
            this.label4.TabIndex = 9;
            this.label4.Text = "ID #:";
            // 
            // TxtID
            // 
            this.TxtID.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtID.Location = new System.Drawing.Point(138, 9);
            this.TxtID.Name = "TxtID";
            this.TxtID.Size = new System.Drawing.Size(159, 20);
            this.TxtID.TabIndex = 8;
            this.TxtID.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.SearchField_KeyPress);
            // 
            // label5
            // 
            this.label5.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(324, 9);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(347, 39);
            this.label5.TabIndex = 10;
            this.label5.Text = "You may search for only one of these at any time. Leave all fields blank to show " +
    "all accounts.";
            // 
            // BtnClear
            // 
            this.BtnClear.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnClear.Location = new System.Drawing.Point(463, 76);
            this.BtnClear.Name = "BtnClear";
            this.BtnClear.Size = new System.Drawing.Size(130, 27);
            this.BtnClear.TabIndex = 11;
            this.BtnClear.Text = "Clear Search";
            this.BtnClear.UseVisualStyleBackColor = true;
            this.BtnClear.Click += new System.EventHandler(this.BtnClear_Click);
            // 
            // FrmAccounts
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(96F, 96F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Dpi;
            this.ClientSize = new System.Drawing.Size(683, 499);
            this.Controls.Add(this.BtnClear);
            this.Controls.Add(this.label5);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.TxtID);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.TxtBadgeNumber);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.TxtEmail);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.TxtLastName);
            this.Controls.Add(this.BtnSearch);
            this.Controls.Add(this.LstPeople);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MinimumSize = new System.Drawing.Size(699, 440);
            this.Name = "FrmAccounts";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent;
            this.Text = "Capricon Accounts";
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.ListView LstPeople;
        private System.Windows.Forms.ColumnHeader colID;
        private System.Windows.Forms.ColumnHeader colFirstName;
        private System.Windows.Forms.ColumnHeader colLastName;
        private System.Windows.Forms.ColumnHeader colEmail;
        private System.Windows.Forms.ColumnHeader colPhone;
        private System.Windows.Forms.Button BtnSearch;
        private System.Windows.Forms.TextBox TxtLastName;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.TextBox TxtEmail;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.TextBox TxtBadgeNumber;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.TextBox TxtID;
        private System.Windows.Forms.ImageList SortImages;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.Button BtnClear;
    }
}